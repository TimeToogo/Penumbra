<?php

namespace Penumbra\Utilities\Cache;

class MemoryCache implements ICache {
    private $Cache = [];
    const ValueKey = 'Value';
    const ExpiryKey = 'Expiry';
    
    public function Retrieve($Key) {
        if(!isset($this->Cache[$Key]))
            return null;
        
        $CachedValue = $this->Cache[$Key];
        if($this->IsExpired($CachedValue[self::ExpiryKey])) {
            $this->Delete($Key);
            return null;
        }
        
        return $CachedValue[self::ValueKey];
    }

    public function Contains($Key) {
        return $this->Retrieve($Key) !== null;
    }
    
    public function Save($Key, $Value, $ExpirySeconds = false, $Overwrite = true) {
        if(!$Overwrite && $this->Retrieve($Key) !== null) {
            return false;
        }
        
        $ExpiryTime = $this->GetExpiryTime($ExpirySeconds);
        $this->Cache[$Key] = [self::ValueKey => $Value, self::ExpiryKey => $ExpiryTime];
        
        return true;
    }
    
    
    public function Delete($Key) {
        if(!$this->Contains($Key))
            return false;
        else {
            unset($this->Cache[$Key]);
            return true;
        }
    }
    
    private function GetExpiryTime($ExpirySeconds) {
        if($ExpirySeconds === false)
            return null;
        $Negative = $ExpirySeconds < 0;
        $ExpirySeconds = abs($ExpirySeconds);
        
        $ExpiryInterval = new \DateInterval('PT' . $ExpirySeconds . 'S');
        if($Negative)
            $ExpiryInterval->invert = 1;
        return $this->Now()->add($ExpiryInterval);
    }
    private function IsExpired(\DateTime $Expiry = null) {
        if($Expiry === null)
            return false;
        else
            return $this->Now() > $Expiry;
    }
    private function Now() {
        return new \DateTime('now', new \DateTimeZone('UTC'));
    }
        
    public function Clear() {
        $this->Cache = [];
    }
}

?>
