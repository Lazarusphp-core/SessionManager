<?php
namespace LazarusPhp\SessionManager;

use LazarusPhp\DatabaseManager\Database;
use App\System\Classes\Required\Date;
use DateInterval;
use PDO;
use PDOException;

class Sessions extends Database
{
    private $db;
    private $table;
    private $mysid;
    private $write_expiry;
    private $date;

    public $time;
    public function __construct($rememberme=null)
    {
        // Required To use Contructor of Database Class;
        parent::__construct();
        // End Parent Constructor;

        $this->table = "sessions";
        $this->date = new Date();
        // Auto Login
        $this->time = 60*60*24*7;
    }

    public function RegenerateId()
    {
        return session_regenerate_id(true);
    }

    public function Start()
    {
        session_set_save_handler(
            [$this,"open"],
            [$this,"close"],
            [$this,"read"],
            [$this,"write"],
            [$this,"destroy"],
            [$this,"gc"],
        );

        if(session_start())
        {
            // Set Cookie to ReCirculate the browser value on Boot
            setcookie(session_name(), session_id(), time() + $this->time,"/",$_SERVER['HTTP_HOST']);
        }
    
    }

    public function open()
    {
       return true;
    }


    public function read($sessionID) :string
    {
        
        // $this->mysid = session_id();
        $stmt = $this->sql("SELECT * FROM ".$this->table." WHERE session_id = :sessionID",[":sessionID"=>session_id()])
        ->One(PDO::FETCH_ASSOC);
        return $stmt ? $stmt["data"] : ""; 
    }

    public function write($sessionID, $data): bool
    {

        $date  = $this->date->AddDate("now")->add(new DateInterval("P365D"))->format("Y-m-d H:i:s");
        $params =  [":sessionID"=>session_id(),":data"=>$data,":expiry"=>$date];
        $this->GenerateQuery("REPLACE INTO " . $this->table . " (session_id,data,expiry) VALUES(:sessionID,:data,:expiry)",$params);
        return true;
       
    }

    public function close(): bool
    {

        // $this->CloseDb();
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
        $expiry = $this->date->AddDate("now")->format("Y-m-d");

        try {
            $params = [":expiry"=>$expiry];
            $this->GenerateQuery("DELETE FROM sessions WHERE expiry  < :expiry",$params);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage() . $e->getCode());
        }
    }

    // Add A Session Watch Script to run;

    // Unset All Sessions but Do not destroy
    public function UnsetAll($key = null)
    {
        $this->sql("select * from $this->table where session_id=:sessionID", [":sessionID" => session_id()])->One();

        if (session_id() == $session->session_id) {
            foreach ($_SESSION as $key => $value) {
                unset($_SESSION[$key]);
            }
        }
    }
/** Modify Sessions if anything changes
 * Boot the Sessions on bootup with Core.php
 */



 public function WatchSession()
 {
    $date = $this->date->AddDate("now");
    $session = $this->AddParams(":sessionID",session_id())->One("SELECT * FROM sessions WHERE session_id = :sessionID");
    if($session->expiry < $this->date->AddDate("now")->format("Y-m-d")) {
        session_regenerate_id();
    }
    else
    {
        return false;
    }
 }




    
}