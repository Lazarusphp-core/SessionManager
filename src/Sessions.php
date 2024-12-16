<?php

namespace LazarusPhp\SessionManager;
use LazarusPhp\DateManager\Date;
use LazarusPhp\SessionManager\Interfaces\SessionControl;
use LazarusPhp\SessionManager\SessionWriter;
use PDO;
use PDOException;

use function PHPSTORM_META\elementType;

class Sessions
{


    private SessionControl $sessionControl;
    private $config;
    private $init = false;
    private $customboot;

    
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



    // End Assignment Properties

    // Constructor and Destructors


    public function __construct($customboot = false)
        {
            if($customboot === true)
            {
                $this->customboot = true;
            }
            else
            {
                $this->customboot = false;
            }
        }



    public function init(array $classname, $config = null): void
    {

        // Detect if the class exists
        $this->config["customboot"] = false;
        (!is_null($config) && is_array($config)) ? $this->config = $config : $this->config = null;


        if (is_array($classname)) {
            if (class_exists($classname[0])) {
                $this->sessionControl = new $classname[0]($this->config);
            } else {
                trigger_error("CLass Not Found");
            }
        } else {
            trigger_error("The Requsted clsss is not in an array format");
        }

        // Start Session Apart from  Creating it within the database no data will be stored.
        session_set_save_handler(
            [$this, "open"],
            [$this, "close"],
            [$this, "read"],
            [$this, "write"],
            [$this, "destroy"],
            [$this, "gc"],
        );
        if (session_status() !== PHP_SESSION_ACTIVE) {
            // Load session_start
            if (session_start()) {
                if (is_bool($this->customboot) && $this->customboot === true) {
                    $this->sessionControl->customBoot();
                }
            }
        }

      
    }
 

    public function open():bool
    {
        return $this->sessionControl->openQuery();
    }

    // Session Handler Methods;
    public function read($sessionID) :string
    {
        
        // $this->mysid = session_id();
        $stmt = $this->sessionControl->readQuery($sessionID);
        return $stmt ? $stmt["data"] : ""; 
    }

    public function write($sessionID, $data): bool
    {
        if($this->sessionControl->writeQuery($sessionID,$data,$this->date,$this->format));
        return true;
       
    }



    public function close(): bool
    {
        return $this->sessionControl->closeQuery();
    }

    public function destroy($sessionID=null): bool
    {
       if($this->sessionControl->destroyQuery($sessionID))
       {
         return true;
       }
       else
       {
        trigger_error("Failed to destroy Session");
       }
    
   
    }

    public function gc()
    {
        return $this->sessionControl->gcQuery($this->date);
    }
}