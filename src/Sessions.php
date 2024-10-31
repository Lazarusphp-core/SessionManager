<?php

namespace LazarusPhp\SessionManager;

use LazarusPhp\DatabaseManager\Database;
use LazarusPhp\DateManager\Date;
use PDO;
use PDOException;
use App\System\Classes\Injection\Container;

class Sessions
{

    public int $expiry = 1;
    public string $format = "y-m-d H:i:s";
    private $table = "sessions";
    private $date;
    private $database;
    
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
    
    private function generateSession():void
    {
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
        $this->date = Date::withAddedTime("now","P".$this->expiry."D");
        // Include the sessionHandler
        $this->generateSession();
        // End Session Handler

        // Detect Status of session
        if (session_status() !== PHP_SESSION_ACTIVE) {
            // Load session_start
            if (session_start()) {
                // Set the cookie to keep session active between the database and the browser
                setcookie(session_name(), session_id(), Date::asTimestamp($this->date), "/", "." . $_SERVER['HTTP_HOST']);
            }
        }
    }

    
    // Session Handler Methods;
    public function open():bool
    {
       return true;
    }


    public function read($sessionID) :mixed
    {
        $container = new Container([QueryWriter::class]);
        $stmt = $container->method("readQuery",$this->table,$sessionID);
        // $stmt = $this->sql("SELECT * FROM ".$this->table." WHERE session_id = :sessionID",[":sessionID"=>$sessionID])
        // ->One(PDO::FETCH_ASSOC);

        return $stmt ? $stmt["data"] : ""; 
    }

    public function write($sessionID, $data): bool
    {
        $container = new Container([QueryWriter::class]);
        $container->method("writeQuery",$sessionID,$data,$this->date,$this->format,$this->table);
        return true;
       
    }

    public function close(): bool
    {
        return true;
    }

    public function destroy($sessionID): bool
    {
        $container = new Container(["QueryWriter::class"]);
        $container->method("destroyQuery",$sessionID,$this->table);
        return true;
    }

    public function gc()
    {
    
        $container = new Container(["QueryWriter::class"]);
        $container->method("gcQuery");
        unset($_COOKIE[session_name()]);
    }
    // End Session handler Methods
}