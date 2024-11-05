<?php

namespace LazarusPhp\SessionManager;

use LazarusPhp\DatabaseManager\Database;
use LazarusPhp\DateManager\Date;
use PDO;
use PDOException;

class Sessions extends Database
{

    public int $expiry = 7;
    public string $format = "y-m-d H:i:s";
    private $table = "sessions";
    private $date;
    
    // Magic Methods to control Sessions.
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function __get($name)
    {
        if(array_key_exists($name,$_SESSION))
        {
            return $_SESSION[$name];
        }
    }

    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    public function __unset($name)
    {
        unset($_SESSION[$name]);
    }
    
    // Assignment Properties

    public function setExpiry(int $expiry):void
    {
        $this->expiry = $expiry;
    }

    public function setFormat(string $format):void
    {
        $this->format = $format;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }


    // End Assignment Properties
    
    // Generate Session handler

    private function currentDate() {
        $this->date = Date::withAddedTime("now","P".$this->expiry."D");
    }


    public function instantiate():void
    {
        $this->currentDate();
        // Start Session Apart from  Creating it within the database no data will be stored.
        session_set_save_handler(
            [$this,"open"],
            [$this,"close"],
            [$this,"read"],
            [$this,"write"],
            [$this,"destroy"],
            [$this,"gc"],
        );
    }

    public function start(): void
    {
        $this->currentDate();
        // Detect Status of session
        if (session_status() !== PHP_SESSION_ACTIVE) {
            // Load session_start
            if (session_start()) {
                // Set the cookie to keep session active between the database and the browser
                setcookie(session_name(), session_id(), Date::asTimestamp($this->date), "/", "." . $_SERVER['HTTP_HOST']);
            }
        }
    }

    public function open()
    {
       return true;
    }

    
    // Session Handler Methods;
    public function read($sessionID) :string
    {
        
        // $this->mysid = session_id();
        $stmt = $this->sql("SELECT * FROM ".$this->table." WHERE session_id = :sessionID",[":sessionID"=>$sessionID])
        ->One(PDO::FETCH_ASSOC);
        return $stmt ? $stmt["data"] : ""; 
    }

    public function write($sessionID, $data): bool
    {
        $date = $this->date->format($this->format);
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
}