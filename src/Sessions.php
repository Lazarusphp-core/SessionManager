<?php

namespace LazarusPhp\SessionManager;
use LazarusPhp\DateManager\Date;
use LazarusPhp\SessionManager\Interfaces\SessionControl;
use LazarusPhp\SessionManager\CoreFiles\SessionCore;
use App\System\Writers\SessionWriter;
use Error;
use SessionHandler;
use PDO;
use PDOException;

class Sessions extends SessionCore
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

    // End Assignment Properties

    public function instantiate(array $classname,array $config = []): void
    {
      

        $config = $this->setConfig($config);
        // Return config values
        if(is_array($classname))
        {
            if(class_exists($classname[0]))
            {
                $handle = new $classname[0]();
                if (session_status() !== PHP_SESSION_ACTIVE) {
                
                    // Set Cookie Params
                    session_set_cookie_params(
                    [
                        'path' => $config['path'] ?? '/',
                        'domain' => $config['domain'] ?? ".".$_SERVER['HTTP_HOST'],
                        'secure' => $config['secure'] ?? isset($_SERVER['HTTPS']),
                        'httponly' => $config['httponly'] ?? true,
                        'samesite' => $config['samesite'] ?? "lax"
                ]);
                    session_set_save_handler($handle);
                    $handle->passConfig($config);
                    setcookie(session_name(), session_id(), Date::asTimestamp(Date::withAddedTime("now","P".$config['days']."D")), "/", "." . $_SERVER['HTTP_HOST']);
                    // Load session_start
                    session_start();
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