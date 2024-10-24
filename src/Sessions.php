<?php

namespace LazarusPhp\SessionManager;

class Sessions extends SessionCore
{

    public function setTz($tz="Europe/London")
    {
        return date_default_timezone_set($tz);
    }

    public function getTz()
    {
        return date_default_timezone_get();
    }
    
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

    public function deleteSessions(...$args): void
    {
        $count = count($args);
        echo $count;
        if($count > 0)
        {
            foreach($args as $arg)
            {
                if(array_key_exists($arg,$_SESSION))
                {
                    echo $arg;
                    unset($_SESSION[$arg]);
                    echo "$arg Deleted";
                }
            }
        }
        else
        {
            session_destroy();
            echo "We Will just delete All Sessions";
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