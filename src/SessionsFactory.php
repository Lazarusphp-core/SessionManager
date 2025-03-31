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
        
        self::create()->$key = $value;
    }

    public static function get($key)
    {
        
        return self::create()->$session->$key;
    }

    public static function destroySessions(...$args)
    {
        self::create()->destroySessions($args);
    }
}