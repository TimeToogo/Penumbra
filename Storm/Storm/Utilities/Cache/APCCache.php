<?php

namespace Storm\Utilities\Cache;

class APCCache implements ICache {
        
    public function Retrieve($Key) {
        return apc_fetch($Key);
    }

    public function Contains($Key) {
        return apc_exists($Key);
    }
    
    public function Save($Key, $Value, $ExpirySeconds = false, $Overwrite = true) {
        if($ExpirySeconds === false)
            $ExpirySeconds = 0;
        if($Overwrite)
            return apc_store($Key, $Value, $ExpirySeconds);
        else
            return apc_add($Key, $Value, $ExpirySeconds);
    }

    public function Delete($Key) {
        return apc_delete($Key);
    }
    
    public function Clear() {
        return apc_clear_cache();
    }
}

?>
