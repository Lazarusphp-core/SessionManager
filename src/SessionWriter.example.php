<?php
namespace LazarusPhp\SessionManager;
use SessionhandlerInterface;

class SessionWriter Implements SessionHandlerInterface
{

    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        /** 
         * Set Some Requirements if needs be here then call by using
         * $this->config["key"];
         */
        $required = [];
        if(is_array($config))
        {
            foreach ($required as $key) {
                if (!array_key_exists($key,$config)) {
                    throw new \InvalidArgumentException("Missing required config key: $key");
                }
            }
        } 
    }

    public function open(?string $path,?string $name):bool
    {
        // Add any logic here that may be used for open, this can just be left empty and return true
        return true;
    }

    public function close():bool
    {
        // Add any logic here that may be used for close, this can just be left empty and return true
        return true;
    }
    public function read(string $sessionID):string | false
    {
        // Add any logic here that may be used for read, code here is used to read session data.
        // Replace below example with own code.
        // $stmt is a requirment to pass data.
        $query = new QueryBuilder();
        $stmt = $query->sql("SELECT * FROM ". $this->config["table"] ." WHERE session_id = :sessionID",[":sessionID"=>$sessionID])
        ->one(PDO::FETCH_ASSOC);
        return $stmt ? $stmt['data'] : '';
    }

    public function write($sessionID,$data):bool
    {
        // Add any logic here that may be used for write, code here is used to write session data.
        // Replace below example with own database code.
        $date = Date::withAddedTime("now","P".$this->config["days"]."D")->format("y-m-d h:i:s");  
        $params =  [":sessionID"=>$sessionID,":data"=>$data,":expiry"=>$date];
        $query = new QueryBuilder();
        $query->asQuery("REPLACE INTO ". $this->config["table"] . "  (session_id,data,expiry) VALUES(:sessionID,:data,:expiry)",$params);
        return true;
    } 
    public function destroy($sessionID): bool
    {
        // Add any logic here that may be used for destroy, code here is used to destroy session data.
        // Replace below example with own database code.
        $query = new QueryBuilder();
        $params = [":sessionID"=>$sessionID];
        $query->asQuery("DELETE FROM ". $this->config["table"] . "  WHERE session_id=:sessionID",$params);
        return true;
    }

    public function gc(int $maxlifetime):int|false
    {
        // Add any logic here that may be used for gc, code here is used to destroy session data.
        // Replace below example with own database code.
        $expiry = Date::create("now");
        $expiry = $expiry->format("y-m-d h:i:s");
        
        try {
            $query = new QueryBuilder();
            $params = [":expiry"=>$expiry];
            $query->asQuery("DELETE FROM ". $this->config["table"] . "  WHERE expiry  < :expiry",$params);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage() . $e->getCode());
        }
    }

    
}