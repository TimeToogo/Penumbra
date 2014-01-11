<?php

namespace Storm\Utilities\Cache;

class PrefixedCache implements ICache {
    private $Prefix;
    private $Cache;
    
    public function __construct($Prefix, ICache $Cache) {
        $this->Prefix = $Prefix;
        $this->Cache = $Cache;
    }

    public function Contains($Key) {
        return $this->Contains($this->Prefix . $Key);
    }

    public function Delete($Key) {
        return $this->Delete($this->Prefix . $Key);
    }

    public function Retrieve($Key) {
        return $this->Retrieve($this->Prefix . $Key);
    }

    public function Save($Key, $Value, $ExpirySeconds = false, $Overwrite = true) {
        return $this->Save($this->Prefix . $Key, $Value, $ExpirySeconds, $Overwrite);
    }
    
    public function Clear() {
        return $this->Cache->Clear();
    }
}

?>
