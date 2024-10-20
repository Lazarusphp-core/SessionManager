<?php
namespace LazarusPhp\SessionManager;

use LazarusPhp\DatabaseManager\Database;
use PDO;
use PDOException;

class SessionCore extends Database
{
    private $db;
    private $table = "";
    private $mysid;
    private $write_expiry;
    public $date;
    private $format = "y-m-d H:i:s";

    private $handle;
    private $days;
    // Unix timestamp for now
    public function __construct()
    {
        parent::__construct();
        // Required To use Contructor of Database Class;

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
    public function start(int $days=0)
    {
        define("DAYS",$days);
        // Auto Login
        if(session_status() !== PHP_SESSION_ACTIVE)
        {
          if(session_start()){
            // Set the cookie Name;
            // unset($_COOKIE[session_name()]);
            // setcookie(session_name(), session_id(), time() + 60*60*24*DAYS,"/",$_SERVER['HTTP_HOST']);
            }
        }
     
    }

/**
 * return bool;
 */  


    public function open()
    {
       return true;
    }


    public function read($sessionID) :mixed
    {
        $stmt = $this->sql("SELECT * FROM ".$this->table." WHERE session_id = :sessionID",[":sessionID"=>session_id()])
        ->One(PDO::FETCH_ASSOC);
        return $stmt ? $stmt["data"] : ""; 
    }

    public function write($sessionID, $data): mixed
    {
        // This needs fixing at some point tomorrow 
        // Currently Working
    
        $days =  DAYS;
        $date = date($this->format,time() + 60*60*24*$days);
        $params =  [":sessionID"=>session_id(),":data"=>$data,":expiry"=>$date];
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
        $expiry = date($this->format,time());

        try {
            $params = [":expiry"=>$expiry];
            $this->GenerateQuery("DELETE FROM sessions WHERE expiry  < :expiry",$params);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage() . $e->getCode());
        }

        unset($_COOKIE[session_name()]);
    }

     
}