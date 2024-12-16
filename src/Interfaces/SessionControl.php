<?php
namespace LazarusPhp\SessionManager\Interfaces;

interface SessionControl
{

    public function __construct(array $config=null);
    public function customBoot():void;
    public function openQuery():bool;
    public function closeQuery():bool;
    public function readQuery(string $sessionID):mixed;
    public function writeQuery(string $sesionId,string|int $data):bool;
    public function destroyQuery($sesionId):bool;
    public function gcQuery():void;
}