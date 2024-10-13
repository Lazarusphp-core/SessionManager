<?php
namespace LazarusPhp\SessionManager;

use LazarusPhp\DatabaseManager\Database;
use App\System\Classes\Date;
use DateTime;
use DateInterval;
use LazarusPhp\SessionManager\SessionWriter;
use PDO;
use PDOException;

class SessionCore extends SessionWriter
{
    private $db;
    private $table = "";
    private $mysid;
    private $write_expiry;
    public $date;

    private $handle;


    private $time;
    // Unix timestamp for now
    private $now;
    public function __construct()
    {
        parent::__construct();
        $this->now = time();
        // Required To use Contructor of Database Class;
        $this->time = $time ?? $this->time = 60*60*24*7;
        $this->date = date("y-m-d h:i:s",$this->now+ $this->time);
    }

    public function instantiate($table=null)
    {
        $this->table = $table ?? $this->table = "sessions";
        session_set_save_handler(
            [$this,"open"],
            [$this,"close"],
            [$this,"read"],
            [$this,"write"],
            [$this,"destroy"],
            [$this,"gc"],
        );
        echo $this->table;
    }
    /**
     * Summary of start
     * @param mixed $time
     * @method newId(true)
     * @return void
     */
    public function start($time=null)
    {

        // Auto Login
        if(session_status() !== PHP_SESSION_ACTIVE)
        {
          if(session_start()){
            $this->newId(true);
            // Set the cookie Name;
            setcookie(session_name(), session_id(), time() + $this->time,"/",$_SERVER['HTTP_HOST']);
            }
        
        }
     
    }

/**
 * return bool;
 */    public function newId():bool
    {
        return session_regenerate_id(true);
    }

    public function open()
    {
       return true;
    }


    public function read($sessionID) :mixed
    {
        
        // $this->mysid = session_id();
        $stmt = $this->sql("SELECT * FROM ".$this->table." WHERE session_id = :sessionID",[":sessionID"=>session_id()])
        ->One(PDO::FETCH_ASSOC);
        return $stmt ? $stmt["data"] : ""; 
    }

    public function write($sessionID, $data): mixed
    {

        $params =  [":sessionID"=>session_id(),":data"=>$data,":expiry"=>$this->date];
        $this->GenerateQuery("REPLACE INTO " . $this->table . " (session_id,data,expiry) VALUES(:sessionID,:data,:expiry)",$params);
        return true;
       
    }

    public function close(): bool
    {
        return true;
    }

    public function destroy($sessionID=null): bool
    {
        $params = [":sessionID"=>session_id()];
        $this->GenerateQuery("DELETE FROM " . $this->table . " WHERE session_id=:sessionID",$params);
        return true;
    
   
    }

    public function gc()
    {
        $expiry = date("y-m-d h:i:s",$this->now);

        try {
            $params = [":expiry"=>$expiry];
            $this->GenerateQuery("DELETE FROM sessions WHERE expiry  < :expiry",$params);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage() . $e->getCode());
        }
    }

     
}