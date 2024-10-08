<?php

namespace LazarusPhp\SessionManager;

class Sessions
{

    public static function boot()
    {
        return new SessionCore();
    }

    public static function Create($name,$value)
    {
        $_SESSION[$name] = $value;
    }

    public static function Delete($name)
    {
        unset($_SESSION[$name]);
    }


}