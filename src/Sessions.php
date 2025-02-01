<?php

namespace LazarusPhp\SessionManager;
use LazarusPhp\DateManager\Date;
use LazarusPhp\SessionManager\Interfaces\SessionControl;
use App\System\Writers\SessionWriter;
use SessionHandler;
use PDO;
use PDOException;

class Sessions
{


    private SessionControl $sessionControl;
    private $config = [];
    private $init = false;


    
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

    public function  setConfig($config)
    {
        foreach($config as $key => $value)
        {
            $this->config[$key] = $value;
        }

        return (object) $this->config;
    }


    // End Assignment Properties

    public function init(array $classname,array $config = []): void
    {

        $config = $this->setConfig($config);
        if(is_array($classname))
        {
            if(class_exists($classname[0]))
            {
                $handle = new $classname[0]();
                if (session_status() !== PHP_SESSION_ACTIVE) {
                    session_set_save_handler($handle);
                    // Load session_start
                    if (session_start()) {
                        $days = 7;
                        setcookie(session_name(), session_id(), Date::asTimestamp(Date::withAddedTime("now","P".$days."D")), "/", "." . $_SERVER['HTTP_HOST']);
                    }
                }
            }
            else
            {
                throw new \Exception("Session Handler must be a class");
            }
        }
        else{
            throw new \Exception("Session Handler must be an array");
        }
      
       

      
    }
 
}