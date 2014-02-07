<?php

namespace Storm\Core\Containers;

/**
 * The Map is a bi-directional instance collection. 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
    
    /**
     * Creates a new map from two arrays of instances.
     * 
     * @param array $Instances The array of objects to map.
     * @param array $ToInstances The array of object to map to.
     * @return \Storm\Core\Containers\Map The created map
     * @throws \Storm\Core\StormException If the amount of instances is not equal to the amount of to instances
     */
    public static function From(array $Instances, array $ToInstances) {
        if(count($Instances) !== count($ToInstances)) {
            throw new \Storm\Core\StormException('Lengths of supplied arrays must be equal: %d != %d', count($Instances), count($ToInstances));
        }
        
        $Map = new Map();
        $InstancesUnkeyed = array_values($Instances);
        $ToInstancesUnkeyed = array_values($ToInstances);
        foreach($InstancesUnkeyed as $Key => $Instance) {
            $Map->MapNew($Instance, $ToInstancesUnkeyed[$Key]);
        }
        
        return $Map;
    }
    
    /**
     * @return array
     */
    public function GetInstances() {
        return iterator_to_array($this->IteratingStorage);
    }
    
    /**
     * @return array
     */
    public function GetToInstances() {
        return iterator_to_array($this->InverseIteratingStorage);
    }
    
    /**
     * Maps an instance to another instance. Previous mappings for either instances are replaced.
     * 
     * @param object $Instance
     * @param object $ToInstance
     * @return Map
     * @throws \Storm\Core\StormException If either of the parameters is not an object
     */
    public function Map($Instance, $ToInstance) {
        if(!is_object($Instance) || !is_object($ToInstance)) {
            throw new \Storm\Core\StormException('Map only supports mapping object: %s and %s given', gettype($Instance), gettype($ToInstance));
        }
        
        if($this->Storage->contains($Instance) || $this->InversedStorage->contains($Instance)) {
            $this->Unmap($Instance);
        }
        $this->MapNew($Instance, $ToInstance);
        
        return $this;
    }
    private function MapNew($Instance, $ToInstance) {
        $this->IteratingStorage->attach($Instance);
        $this->InverseIteratingStorage->attach($ToInstance);
        $this->Storage->attach($Instance, $ToInstance);
        $this->InversedStorage->attach($ToInstance, $Instance);
    }
    
    /**
     * Removes any mapping of the supplied instance. 
     * 
     * @param object $Instance The instance to unmap.
     * @return Map
     */
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
        
        return $this;
    }    
    private function Detach($Instance, $ToInstance) {
        $this->Storage->detach($Instance);
        $this->InversedStorage->detach($ToInstance);
        $this->IteratingStorage->detach($Instance);
        $this->InverseIteratingStorage->detach($ToInstance);
    }
    
    public function getIterator() {
        return $this->IteratingStorage;
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
