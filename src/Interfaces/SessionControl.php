<?php
namespace LazarusPhp\SessionManager\Interfaces;

interface SessionControl
{

    public function customBoot():void;
    public function readQuery(string $sessionID):mixed;
    public function writeQuery(string $sesionId,string|int $data):bool;
    public function destroyQuery($sesionId):bool;
    public function gcQuery():void;
}