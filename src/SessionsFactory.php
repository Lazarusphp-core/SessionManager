<?php
namespace LazarusPhp\SessionManager;

class SessionsFactory
{
    public static function create()
    {
        return new Sessions();
    }

    public static function instantiate(array $classname,array $config = [])
    {
        return (new Sessions())->instantiate($classname, $config);
    }

    public static function set($key,$value)
    {
        $session = new Sessions();
        $session->$key = $value;
    }

    public static function get($key)
    {
        $session = new Sessions();
        return $session->$key;
    }

    public static function destroy($key)
    {
        $session = new Sessions();
        unset($session->$key);
    }
}