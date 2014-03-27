<?php

namespace Penumbra\Utilities\Cache;

class MemcachedCache implements ICache {
    private $Memcached;
    
    public function __construct($Host, $Port = 11211, $PersistentId = null) {
        $this->Memcached = new \Memcached($PersistentId);
        $this->Memcached->addServer($Host, $Port);
    }
    
    public function Retrieve($Key) {
        return $this->Memcached->get($Key);
    }

    public function Contains($Key) {
        $this->Retrieve($Key);
        return $this->Memcached->getResultCode() === \Memcached::RES_SUCCESS;
    }
    
    public function Save($Key, $Value, $ExpirySeconds = false, $Overwrite = true) {
        if($ExpirySeconds === false)
            $ExpirySeconds = 0;
        if($Overwrite)
            return $this->Memcached->set($Key, $Value, $ExpirySeconds);
        else
            return $this->Memcached->add($Key, $Value, $ExpirySeconds);
    }

    public function Delete($Key) {
        return $this->Memcached->delete($Key);
    }
    
    public function Clear() {
        return $this->Memcached->flush();
    }
}

?>
