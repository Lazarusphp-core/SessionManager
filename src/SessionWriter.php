<?php
namespace LazarusPhp\SessionManager;
use LazarusPhp\DatabaseManager\QueryBuilder;
use LazarusPhp\DateManager\Date;
use LazarusPhp\SessionManager\Interfaces\SessionControl;
use PDO;

class SessionWriter implements SessionControl
{

    private $table;
    private $date;
    private $expiry;
    private $format;


    public function __construct($sessionName=null)
    {
        echo $sessionName;
   
        $this->DateConfig();
    }

    public function DateConfig()
    {
        $this->expiry = 21;
        $this->table = "sessions";
        $this->format = "y-m-d h:i:s";
    }

    public function setCookie() :bool
    {
        return  setcookie(session_name(), session_id(), Date::asTimestamp(Date::withAddedTime("now","P".$this->expiry."D")), "/", "." . $_SERVER['HTTP_HOST']);
            
    }
    public function readQuery($sessionID)
    {
        $query = new QueryBuilder();
        $stmt = $query->sql("SELECT * FROM ".$this->table." WHERE session_id = :sessionID",[":sessionID"=>$sessionID])
        ->one(PDO::FETCH_ASSOC);
        return $stmt;
    }

    public function writeQuery($sessionID,$data):bool
    {
        $date = Date::withAddedTime("now","P".$this->expiry."D")->format($this->format);
        $params =  [":sessionID"=>$sessionID,":data"=>$data,":expiry"=>$date];
        $query = new QueryBuilder();
        $query->asQuery("REPLACE INTO " . $this->table . " (session_id,data,expiry) VALUES(:sessionID,:data,:expiry)",$params);
        return true;
    } 
    public function destroyQuery($sessionID): bool
    {
        $query = new QueryBuilder();
        $params = [":sessionID"=>$sessionID];
        $query->asQuery("DELETE FROM " . $this->table . " WHERE session_id=:sessionID",$params);
        return true;
    }

    public function gcQuery()
    {
        $expiry = Date::create("now");
        $expiry = $expiry->format("y-m-d h:i:s");

        try {
            $query = new QueryBuilder();
            $params = [":expiry"=>$expiry];
            $query->asQuery("DELETE FROM sessions WHERE expiry  < :expiry",$params);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage() . $e->getCode());
        }
    }

    
}