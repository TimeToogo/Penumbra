<?php

namespace Storm\Utilities\Cache;

class XcacheCache implements ICache {
    public function __construct() {
        
    }
    
    public function Retrieve($Key) {
        return unserialize(xcache_get($Key));
    }

    public function Contains($Key) {
        return xcache_isset($Key);
    }
    
    public function Save($Key, $Value, $ExpirySeconds = false, $Overwrite = true) {
        if(!$Overwrite && $this->Contains($Key))
            return;
        if($ExpirySeconds === false) {
            return xcache_set($Key, $Value);
        }
        else {
            return xcache_set($Key, serialize($Value), $ExpirySeconds);
        }
    }

    public function Delete($Key) {
        return xcache_unset($Key);
    }
}

?>
