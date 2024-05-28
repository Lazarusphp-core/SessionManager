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
    public function __construct($is_active=false)
    {
        $this->db = new Database();
        $this->table = "sessions";

        session_set_save_handler(
            [$this,"OpenSession"],
            [$this,"CloseSession"],
            [$this,"ReadSession"],
            [$this,"WriteSession"],
            [$this,"DestroySession"],
            [$this,"GCSession"],
        );

        if($is_active == false)
        {  
            session_start();
        }
    }

    public function OpenSession()
    {
       return true;
    }


    public function ReadSession() :string
    {
        $this->mysid = session_id();
        $stmt = $this->db->AddParams(":sessionID",$this->mysid)
        ->One("SELECT * FROM ".$this->table." WHERE session_id = :sessionID",PDO::FETCH_ASSOC);
        
        if($stmt)
        {
        return $stmt ? $stmt["data"] : ""; 
        }
        return false;
    }

    public function WriteSession($sessionID, $data): bool
    {

        $date  = Date::AddDate("now")->add(new DateInterval("P1Y"));
        $date = $date->format("Y-m-d");
        $this->db->AddParams(":sessionID", session_id());
        $this->db->AddParams(":data", $data);
        $this->db->AddParams(":expiry", $date);
        if ($this->db->GenerateSql("REPLACE INTO " . $this->table . " (session_id,data,expiry) VALUES(:sessionID,:data,:expiry)")); {
            return true;
        }
    }

    public function CloseSession(): bool
    {

        $this->db->CloseDb();
        return true;
    }

    public function DestroySession($sessionID): bool
    {
        $this->db->AddParams(":sessionID", $sessionID);
        if ($this->db->GenerateSql("DELETE FROM " . $this->table . " WHERE session_id=:sessionID")) {
            return true;
        }
        return false;
    }

    public function GCSession()
    {
        return true;
    }


    public static function Create($name,$value)
    {
        $_SESSION[$name] = $value;
    }

    public static function GetSession($name)
    {
        return $_SESSION[$name];
    }



    
}