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
                
                    session_set_save_handler($handle);
                    $handle->passConfig($config);

                    $expiry = Date::asTimestamp(Date::withAddedTime("now", "P" . $config['days'] . "D"));
                    if (!is_int($expiry)) {
                        throw new \Exception("Invalid expiration timestamp generated for cookie.");
                    }

                    
                    if (!empty($config['sessionName'])) {
                        session_name($config['sessioname']);
                    }

                    session_set_cookie_params([
                        "lifetime" => $expiry,
                        "path" => ($config["path"] ?? "/"),
                        "domain"=> $_SERVER['HTTP_HOST'],
                        "secure"=> ($config["secure"] ?? false),
                        "httponly"=>($config["httponly"] ?? false),
                        "samesite"=>($config["samesite"] ?? "lax"),
                    ]);
                    
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

    public function deleteSessions(...$args)
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