<?php

namespace Storm\Core\Object;

abstract class PropertyData implements \IteratorAggregate, \ArrayAccess {
    private $EntityMap;
    private $EntityType;
    private $PropertyData;
    
    public function __construct(EntityMap $EntityMap, array $PropertyData = array()) {
        $this->EntityMap = $EntityMap;
        $this->EntityType = $EntityMap->GetEntityType();
        $this->PropertyData = $PropertyData;
    }
    
    /**
     * @return EntityMap
     */
    final public function GetEntityMap() {
        return $this->EntityMap;
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }

    final protected function GetPropertyData() {
        return $this->PropertyData;
    }
    
    final public function SetProperty(IProperty $Property, $Data) {
        if(!$this->EntityMap->HasProperty($Property))
            throw new \InvalidArgumentException('$PropertyOrPropertyName must be a valid property of EntityMap ' . get_class($this->EntityMap));
        
        $this->AddProperty($Property, $Data);
    }
    
    protected function AddProperty(IProperty $Property, $Data) {
        $this->PropertyData[$Property->GetIdentifier()] = $Data;
    }

    final public function getIterator() {
        return new \ArrayIterator($this->PropertyData);
    }

    final public function offsetExists(IProperty $Property) {
        return isset($this->PropertyData[$Property->GetIdentifier()]);
    }
    
    final public function offsetGet(IProperty $Property) {
        return $this->PropertyData[$Property->GetIdentifier()];
    }

    final public function offsetSet(IProperty $Property, $Data) {
        $this->SetProperty($Property, $Data);
    }

    final public function offsetUnset(IProperty $Property) {
        unset($this->PropertyData[$Property->GetIdentifier()]);
    }
    public function GetProperty($Identifier) {
        return $this->EntityMap->GetProperty($Identifier);
    }
    
    final public function Matches(PropertyData $Data) {
        if(!$this->GetEntityType !== $Data->GetEntityType()) {
            return false;
        }
        
        return ksort($this->PropertyData) === ksort($Data->PropertyData);
    }
}

?>