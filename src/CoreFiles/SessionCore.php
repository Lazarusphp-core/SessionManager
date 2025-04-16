<?php

namespace LazarusPhp\SessionManager\CoreFiles;

class SessionCore
{
    private $errors = [];
    private $config = [];

    public function setConfig(array $config)
    {
        $count = count($config);
        $keys = array_keys($config);
        $supportedKeys= ["days","table"];
        
        if(count($config) >= 1)
        {
           
            $this->getUnsupportedKeys($keys,$supportedKeys);

            if(count($this->errors) >= 1)
            {
            
            }
            else
            {
                foreach($config as $key => $value)
                {
                    
                    if(!array_key_exists($key,$this->config)){
                        $this->config[$key] = $value;
                        }
                }
            }
        }
        else
        {
            $this->config = ["days" => 7,"table" => "sessions"];
        }
        return $this->config;
    }

    private function getUnsupportedKeys($keys,$supportedKeys)
    {
        $unsupported = array_diff($keys,$supportedKeys);
        if($unsupported)
        {
            foreach($unsupported as $unsupported)
            {
                $this->errors[] = "Error Found with Key : $unsupported is not supported";
            }
        }
    }

}