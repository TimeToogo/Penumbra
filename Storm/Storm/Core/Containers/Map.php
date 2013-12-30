<?php

namespace Storm\Core\Containers;

final class Map implements \IteratorAggregate, \ArrayAccess {
    private $IteratingStorage;
    private $InverseIteratingStorage;
    private $Storage;
    private $InversedStorage;
    
    public function __construct() {
        $this->IteratingStorage = new \SplObjectStorage();
        $this->InverseIteratingStorage = new \SplObjectStorage();
        $this->Storage = new \SplObjectStorage();
        $this->InversedStorage = new \SplObjectStorage();
    }
    
    public static function From(array $Instances, array $ToInstances) {
        if(count($Instances) !== count($ToInstances)) {
            throw new \InvalidArgumentException('Inequal length arrays given');
        }
        
        $Map = new Map();
        $InstancesUnkeyed = array_values($Instances);
        $ToInstancesUnkeyed = array_values($ToInstances);
        foreach($InstancesUnkeyed as $Key => $Instance) {
            $Map->MapNew($Instance, $ToInstancesUnkeyed[$Key]);
        }
        
        return $Map;
    }
    
    public function GetInstances() {
        return iterator_to_array($this->IteratingStorage);
    }
    
    public function GetToInstances() {
        return iterator_to_array($this->InverseIteratingStorage);
    }

    /**
     * @return Map
     */
    public function Map($Instance, $ToInstance) {
        if(!is_object($Instance) || !is_object($ToInstance)) {
            throw new \InvalidArgumentException('$Instance and $ToInstance must be valid object intances');
        }
        
        $this->Unmap($Instance);
        $this->MapNew($Instance, $ToInstance);
        
        return $this;
    }
    private function MapNew($Instance, $ToInstance) {
        $this->IteratingStorage->attach($Instance);
        $this->InverseIteratingStorage->attach($ToInstance);
        $this->Storage->attach($Instance, $ToInstance);
        $this->InversedStorage->attach($ToInstance, $Instance);
    }

    public function Unmap($Instance) {
        if(!is_object($Instance))
            return;
        if(!$this->Storage->contains($Instance) && !$this->InversedStorage->contains($Instance))
            return;
        
        if($this->Storage->contains($Instance)) {
            $ToInstance = $this->Storage[$Instance];
            
            $this->Detach($Instance, $ToInstance);
        }
        
        if($this->InversedStorage->contains($Instance)) {
            $ToInstance = $Instance;
            $Instance = $this->InversedStorage[$ToInstance];
            
            $this->Detach($Instance, $ToInstance);
        }
    }
    
    private function Detach($Instance, $ToInstance) {
        $this->Storage->detach($Instance);
        $this->InversedStorage->detach($ToInstance);
        $this->IteratingStorage->detach($Instance);
        $this->InverseIteratingStorage->detach($ToInstance);
    }
    
    public function getIterator() {
        return new \IteratorIterator($this->IteratingStorage);
    }

    public function offsetExists($Instance) {
        return $this->Storage->contains($Instance) || $this->InversedStorage->contains($Instance);
    }

    public function offsetGet($Instance) {
        if($this->Storage->contains($Instance))
            return $this->Storage[$Instance];
        else if($this->InversedStorage->contains($Instance))
            return $this->InversedStorage[$Instance];
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
