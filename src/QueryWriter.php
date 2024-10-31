<?php
namespace LazarusPhp\SessionManager;
use LazarusPhp\DatabaseManager\Database;
use PDO;

class QueryWriter extends Database
{

    public function readQuery($table,$sessionID)
    {
        $this->sql("SELECT * FROM ".$table." WHERE session_id = :sessionID",[":sessionID"=>$sessionID])
        ->One(PDO::FETCH_ASSOC);
        return $this;
    }

    public function writeQuery($sessionID,$data,$date,$format,$table)
    {
        $params =  [":sessionID"=>$sessionID,":data"=>$data,":expiry"=>$date->format($format)];
        $this->GenerateQuery("REPLACE INTO " . $table . " (session_id,data,expiry) VALUES(:sessionID,:data,:expiry)",$params);
        return $this;
    }

    public function destroyQuery($sessionID,$table)
    {
        $params = [":sessionID"=>$sessionID];
        $this->GenerateQuery("DELETE FROM " . $table . " WHERE session_id=:sessionID",$params);
        return $this;
    }

    public function gcQuery()
    {
        $expiry = Date::create("now");
        try {
            $params = [":expiry"=>$expiry];
            $this->GenerateQuery("DELETE FROM sessions WHERE expiry  < :expiry",$params);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage() . $e->getCode());
        }
    }

}