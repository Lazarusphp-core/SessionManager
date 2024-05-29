<?php
namespace LazarusPhp\SessionManager;

use LazarusPhp\DatabaseManager\Database;
use App\System\Classes\Required\Date;
use DateInterval;
use PDO;

class Sessions
{
    private $db;
    private $table;
    private $mysid;
    private $write_expiry;
    private $date;
    public function __construct()
    {
    
        $this->db = new Database();
        $this->table = "sessions";
        $this->date = new Date();

      
            // session_start();
    
    }

    public function Start()
    {
        session_set_save_handler(
            [$this,"OpenSession"],
            [$this,"CloseSession"],
            [$this,"ReadSession"],
            [$this,"WriteSession"],
            [$this,"DestroySession"],
            [$this,"WatchSession"],
        );

        session_start();
    }

    public function OpenSession()
    {
       return true;
    }

    

    // public function __destruct()
    // {
    //     session_abort();
    // }

    public function ReadSession($sessionID) :string
    {
        
        // $this->mysid = session_id();
        $stmt = $this->db->AddParams(":sessionID",session_id())
        ->One("SELECT * FROM ".$this->table." WHERE session_id = :sessionID",PDO::FETCH_ASSOC);
        return $stmt ? $stmt["data"] : ""; 
    }

    public function WriteSession($sessionID, $data): bool
    {

        $date  = $this->date->AddDate("now")->add(new DateInterval("P1Y"))->format("Y-m-d H:i:s");
        $this->db->AddParams(":sessionID", session_id());
        $this->db->AddParams(":data", $data);
        $this->db->AddParams(":expiry", $date);
        $this->db->GenerateSql("REPLACE INTO " . $this->table . " (session_id,data,expiry) VALUES(:sessionID,:data,:expiry)");
        return true;
       
    }

    public function CloseSession(): bool
    {

        // $this->db->CloseDb();
        return true;
    }

    public function DestroySession($sessionID): bool
    {
        $this->db->AddParams(":sessionID", session_id());
        $this->db->GenerateSql("DELETE FROM " . $this->table . " WHERE session_id=:sessionID");
        return true;
    
   
    }

    public function GCSession()
    {
        return true;
    }

    // Add A Session Watch Script to run;

    // Unset All Sessions but Do not destroy
    public function UnsetAll($key=null)
    {
        $session = $this->db->AddParams(":sessionID", session_id())->One("select * from $this->table where session_id=:sessionID");
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
    $session = $this->db->AddParams(":sessionID",session_id())->One("SELECT * FROM sessions WHERE session_id = :sessionID");
    if($session->expiry < $this->date->AddDate("now")->format("Y-m-d")) {
        session_regenerate_id();
    }
    else
    {
        return false;
    }
 }





    public function Create($name,$value)
    {
        $_SESSION[$name] = $value;
    }

    public function GetSession($name)
    {
        return $_SESSION[$name];
    }



    
}