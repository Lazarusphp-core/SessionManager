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
    private $config;
    private $init = false;


    
    // Magic Methods to control Sessions.
    public function __set(string $name, string|int $value)
    {
        $_SESSION[$name] = $value;
    }

    public function __get(string $name)
    {
        if(array_key_exists($name,$_SESSION))
        {
            return $_SESSION[$name];
        }
        else
        {
            trigger_error("Undefined property: $name");
        }   
    }

    public function __isset(string $name)
    {
        return isset($_SESSION[$name]);
    }

    public function __unset(string $name)
    {
        unset($_SESSION[$name]);
    }

    public function setConfig(array $config)
    {
        
        if(count($config) === 0)
        {
            $config = ["days" => 7,"table" => "sessions"];

        }
        else
        {
            foreach($config as $key => $value)
    {
             if(!array_key_exists($key,$config)){
                $config[$key] = $value;
                }
            }
        }
            return $config;
        // return (object) $config;
    }


    // End Assignment Properties

    public function instantiate(array $classname,array $config = []): void
    {
     
        $this->config = $this->setConfig($config);
        // Return config values
        if(is_array($classname))
        {
            if(class_exists($classname[0]))
            {
                $handle = new $classname[0]();
                if (session_status() !== PHP_SESSION_ACTIVE) {
                    session_set_save_handler($handle);
                    $handle->passConfig($this->config);
                    // Load session_start
                    if (session_start()) {
                        setcookie(session_name(), session_id(), Date::asTimestamp(Date::withAddedTime("now","P".$this->config['days']."D")), "/", "." . $_SERVER['HTTP_HOST']);
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

    public function destroySessions(...$args)
    {
        if(count($args) === 0)
        {
            session_destroy();
        }
        else
        {
            foreach($args as $arg)
            {
                if (is_array($arg)) {
                    foreach ($arg as $key) {
                        unset($_SESSION[$key]);
                    }
                } else {
                    unset($_SESSION[$arg]);
                }
            }
        }
    }
}