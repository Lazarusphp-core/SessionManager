<?php

namespace LazarusPhp\SessionManager;

class Sessions extends SessionCore
{


    
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

    public function deleteSingleSession($name): void
    {
        if(array_key_exists($name,$_SESSION)){
        unset($_SESSION[$name]);
    }
    else
    {
        trigger_error("No Session for $name found, therefore could not be deleted");
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

    public function listall()
    {
        foreach($_SESSION as $key => $sessions)
        {
            echo "$key = $sessions <br>";
        }
    }

}