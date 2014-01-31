<?php

namespace Storm\Core\Object;

/**
 * The base class representing data stored by an entity's properties.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class PropertyData implements \IteratorAggregate, \ArrayAccess {
    /**
     * @var IProperty[] 
     */
    private $Properties;
    
    /**
     * @var string 
     */
    private $EntityType;
    
    /**
     * @var array 
     */
    private $PropertyData;
    
    public function __construct(array $Properties, array $PropertyData = array()) {
        $IndexedProperties = array();
        foreach ($Properties as $Property) {
            $IndexedProperties[$Property->GetIdentifier()] = $Property;
        }
        $this->Properties =& $IndexedProperties;
        $this->PropertyData = $PropertyData;
    }

    /**
     * @return array<string, mixed>
     */
    final protected function GetPropertyData() {
        return $this->PropertyData;
    }
    
    /**
     * Get another property data instance with new data.
     * 
     * @param array $PropertyData
     * @return PropertyData
     */
    final public function Another(array $PropertyData = array()) {
        $ClonedPropertyData = clone $this;
        $ClonedPropertyData->Properties =& $this->Properties;
        $ClonedPropertyData->PropertyData = array_intersect_key($PropertyData, $this->Properties);
        return $ClonedPropertyData;
    }
    
    
    /**
     * Sets a value for the supplied property
     * 
     * @param IProperty $Property The property to set the value for
     * @param mixed $Data The value to set for the property
     * @throws \InvalidArgumentException
     */
    final public function SetProperty(IProperty $Property, $Data) {
        $Identifier = $Property->GetIdentifier();
        
        if(!isset($this->Properties[$Identifier])) {
            throw new \InvalidArgumentException('$PropertyOrPropertyName must be a property of this property data ');
        }
        
        $this->VerifyProperty($Property, $Identifier);
        $this->PropertyData[$Identifier] = $Data;
    }
    
    protected function VerifyProperty(IProperty $Property, $Identifier) {}

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
    
    final public function GetProperty($Identifier) {
        return $this->EntityMap->GetProperty($Identifier);
    }
    
    /**
     * Whether or not the property data is the same.
     * 
     * @param PropertyData $Data Another property data
     * @return boolean
     */
    final public function Matches(PropertyData $Data) {
        ksort($this->PropertyData);
        ksort($Data->PropertyData);
        
        return $this->PropertyData === $Data->PropertyData;
    }
}

?>