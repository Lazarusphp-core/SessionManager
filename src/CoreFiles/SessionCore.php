<?php

namespace LazarusPhp\SessionManager\CoreFiles;

use Exception;

class SessionCore
{
    private $errors = [];
    private $config = [];

    public function setConfig(array $config)
    {
        $this->config = ["days" => 7, "table" => "sessions"];

        if (count($config) > 0) { {
                foreach ($config as $key => $value) {
                    if (array_key_exists($key, $this->config)) {
                        // OverWrite the value
                        $this->config[$key] = $value;
                    }
                }
                // Merge
            $this->config = array_merge($config,$this->config);
   
            }
        }
        return $this->config;
    }


    protected function generateSessions($regenerate=false)
    {
        if($regenerate==true)
        {
            session_write_close();
            session_start();
            session_regenerate_id(false);
        }
        else
        {
            session_start();
        }
    }   
    
    private function getUnsupportedKeys($keys, $supportedKeys)
    {
        $unsupported = array_diff($keys, $supportedKeys);
        if ($unsupported) {
            foreach ($unsupported as $unsupported) {
                $this->errors[] = "Error Found with Key : $unsupported is not supported";
            }
        }
    }
}
