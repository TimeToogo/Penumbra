<?php

namespace Storm\Core\Containers;

final class Map implements \IteratorAggregate, \ArrayAccess {
    private $IteratingStorage;
    private $Storage;
    
    public function __construct() {
        $this->IteratingStorage = new \SplObjectStorage();
        $this->Storage = new \SplObjectStorage();
    }
    
    public static function From(array $Instances, array $ToInstances) {
        if(count($Instances) !== count($ToInstances))
            throw new \InvalidArgumentException('Unequal length instance arrays');
        
        $Map = new Map();
        $InstancesUnkeyed = array_values($Instances);
        $ToInstancesUnkeyed = array_values($ToInstances);
        foreach($InstancesUnkeyed as $Key => $Instance) {
            $Map->Map($Instance, $ToInstancesUnkeyed[$Key]);
        }
        
        return $Map;
    }

    /**
     * @return Map
     */
    public function Map($Instance, $ToInstance) {
        if(!is_object($Instance) || !is_object($ToInstance))
            throw new \InvalidArgumentException('$Instance and $ToInstance must be valid object intances');
        
        $this->Unmap($Instance);
        $this->Unmap($ToInstance);
        
        $this->IteratingStorage->attach($Instance);
        $this->Storage->attach($Instance, $ToInstance);
        $this->Storage->attach($ToInstance, $Instance);
        
        return $this;
    }

    public function Unmap($Instance) {
        if(!is_object($Instance))
            return;
        if(!$this->Storage->contains($Instance))
            return;
        
        $ToInstance = $this->Storage[$Instance];
        $this->IteratingStorage->detach($Instance);
        $this->IteratingStorage->detach($ToInstance);
        $this->Storage->detach($Instance);
        $this->Storage->detach($ToInstance);
    }

    public function getIterator() {
        return $this->IteratingStorage;
    }

    public function offsetExists($Instance) {
        return $this->Storage->contains($Instance);
    }

    public function offsetGet($Instance) {
        if($this->Storage->contains($Instance))
            return $this->Storage[$Instance];
        else
            return null;
    }

    public function offsetSet($Instance, $ToInstance) {
        $this->Map($Instance, $ToInstance);
    }

    public function offsetUnset($Instance) {
        $this->Unmap($Instance);
    }

}

?>
