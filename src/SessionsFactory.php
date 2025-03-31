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
        return self::create()->instantiate($classname, $config);
    }

    public static function set(string $key,int|string $value)
    {
        
        self::create()->$key = $value;
    }

    public static function get(string $key)
    {
        return self::create()->$key;
    }

    public static function destroySessions(...$args)
    {
        self::create()->destroySessions($args);
    }
}