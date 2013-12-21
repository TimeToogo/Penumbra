<?php

namespace Putty\Cache;

class MemcacheCache implements ICache {
    private $Memcache;
    
    public function __construct($Host, $Port = null, $Timeout = null, $PConnect = false) {
        $this->Memcache = new \Memcache();
        if($PConnect) {
            $this->Memcache->pconnect($Host, $Port, $Timeout);
        }
        else {
            $this->Memcache->connect($Host, $Port, $Timeout);
        }
    }
    
    public function __destruct() {
        $this->Memcache->close();
    }
    
    public function Retrieve($Key) {
        return $this->Memcache->get($Key);
    }

    public function Contains($Key) {
        return $this->Retrieve($Key) !== false;
    }
    
    public function Save($Key, $Value, $ExpirySeconds = false, $Overwrite = true) {
        if($ExpirySeconds === false)
            $ExpirySeconds = 0;
        if($Overwrite)
            return $this->Memcache->set($Key, $Value, null, $ExpirySeconds);
        else
            return $this->Memcache->add($Key, $Value, null, $ExpirySeconds);
    }

    public function Delete($Key) {
        return $this->Memcache->delete($Key);
    }
}

?>
