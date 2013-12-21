<?php

namespace Storm\Core\Object;

abstract class PropertyData implements \IteratorAggregate, \ArrayAccess {
    private $EntityMap;
    private $PropertyData;
    
    public function __construct(EntityMap $EntityMap, array $PropertyData = array()) {
        $this->EntityMap = $EntityMap;
        $this->PropertyData = $PropertyData;
    }
    
    /**
     * 
     * @return EntityMap
     */
    final public function GetEntityMap() {
        return $this->EntityMap;
    }
    
    final public function GetEntityType() {
        return $this->EntityMap->GetEntityType();
    }

    final protected function GetPropertyData() {
        return $this->PropertyData;
    }
    
    final public function SetProperty($PropertyOrPropertyName, $Data) {
        $PropertyName = $this->GetPropertyName($PropertyOrPropertyName);
        if(!$this->EntityMap->HasProperty($PropertyName))
            throw new \InvalidArgumentException('$PropertyOrPropertyName must be a valid property of EntityMap ' . get_class($this->EntityMap));
        
        $this->AddProperty($PropertyName, $Data);
    }
    
    protected function AddProperty($PropertyName, $Data) {
        $this->PropertyData[$PropertyName] = $Data;
    }

    final public function getIterator() {
        return new \ArrayIterator($this->PropertyData);
    }

    final public function offsetExists($PropertyOrPropertyName) {
        return isset($this->PropertyData[$this->GetPropertyName($PropertyOrPropertyName)]);
    }
    
    final public function offsetGet($PropertyOrPropertyName) {
        return $this->PropertyData[$this->GetPropertyName($PropertyOrPropertyName)];
    }

    final public function offsetSet($PropertyOrPropertyName, $Data) {
        $this->SetProperty($PropertyOrPropertyName, $Data);
    }

    final public function offsetUnset($PropertyOrPropertyName) {
        unset($this->PropertyData[$this->GetPropertyName($PropertyOrPropertyName)]);
    }
    
    final protected function GetPropertyName($PropertyOrPropertyName) {
        if($PropertyOrPropertyName instanceof IProperty)
            return $PropertyOrPropertyName->GetName();
        else
            return $PropertyOrPropertyName;
    }
    
    final public function Matches(PropertyData $Data) {
        if(!$this->GetEntityType !== $Data->GetEntityType())
            return false;
        foreach($this->PropertyData as $PropertyName => $Value) {
            if(!isset($Data->PropertyData[$PropertyName]))
                return false;
            if($Value !== $Data->PropertyData[$PropertyName])
                return false;
        }
        
        return true;
    }
}

?>