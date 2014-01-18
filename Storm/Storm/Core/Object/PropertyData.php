<?php

namespace Storm\Core\Object;

/**
 * The base class representing data stored by an entity's properties.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class PropertyData implements \IteratorAggregate, \ArrayAccess {
    /**
     * @var EntityMap 
     */
    private $EntityMap;
    
    /**
     * @var string 
     */
    private $EntityType;
    
    /**
     * @var array 
     */
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
    
    /**
     * @return string
     */
    final public function GetEntityType() {
        return $this->EntityType;
    }

    /**
     * @return array
     */
    final protected function GetPropertyData() {
        return $this->PropertyData;
    }
    
    /**
     * Sets a value for the supplied property
     * 
     * @param IProperty $Property The property to set the value for
     * @param mixed $Data The value to set for the property
     * @throws \InvalidArgumentException
     */
    final public function SetProperty(IProperty $Property, $Data) {
        if(!$this->EntityMap->HasProperty($Property->GetIdentifier())) {
            throw new \InvalidArgumentException('$PropertyOrPropertyName must be a valid property of EntityMap ' . get_class($this->EntityMap));
        }
        
        $this->AddProperty($Property, $Data);
    }
    
    protected function AddProperty(IProperty $Property, $Data) {
        $this->PropertyData[$Property->GetIdentifier()] = $Data;
    }

    final public function getIterator() {
        return new \ArrayIterator($this->PropertyData);
    }

    final public function offsetExists($Property) {
        return isset($this->PropertyData[$Property->GetIdentifier()]);
    }
    
    final public function offsetGet($Property) {
        return $this->PropertyData[$Property->GetIdentifier()];
    }

    final public function offsetSet($Property, $Data) {
        $this->SetProperty($Property, $Data);
    }

    final public function offsetUnset($Property) {
        unset($this->PropertyData[$Property->GetIdentifier()]);
    }
    public function GetProperty($Identifier) {
        return $this->EntityMap->GetProperty($Identifier);
    }
    
    /**
     * Whether or not the property data is the same.
     * 
     * @param PropertyData $Data Another property data
     * @return boolean
     */
    final public function Matches(PropertyData $Data) {
        if($this->EntityType !== $Data->EntityType) {
            return false;
        }
        
        return ksort($this->PropertyData) === ksort($Data->PropertyData);
    }
}

?>