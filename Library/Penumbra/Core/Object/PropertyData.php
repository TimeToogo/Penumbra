<?php

namespace Penumbra\Core\Object;

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
     * @var array 
     */
    protected $Data;
    
    public function __construct(array $Properties, array $PropertyData = []) {
        $IndexedProperties = [];
        foreach ($Properties as $Property) {
            $IndexedProperties[$Property->GetIdentifier()] = $Property;
        }
        $this->Properties = $IndexedProperties;
        $this->Data = array_intersect_key($PropertyData, $this->Properties);
    }
    
    /**
     * @return IProperty[]
     */
    final public function GetProperties(array $Identifiers = null) {
        return $Identifiers === null ? $this->Properties : array_intersect_key($this->Properties, array_flip(array_values($Identifiers)));
    }
        
    /**
     * @return array<string, mixed>
     */
    public function GetData() {
        return $this->Data;
    }
    
    /**
     * @return void
     */
    public function SetData(array $Data) {
        $this->Data = array_intersect_key($Data, $this->Properties);
    }
    
    /**
     * Get another property data instance with new data.
     * 
     * @param array $PropertyData
     * @return static
     */
    final public function Another(array $PropertyData = []) {
        $ClonedPropertyData = clone $this;
        $ClonedPropertyData->Properties =& $this->Properties;
        $ClonedPropertyData->Data = array_intersect_key($PropertyData, $this->Properties);
        return $ClonedPropertyData;
    }
    
    protected function GetPropertyData(IProperty $Property) {
        return $this->Data[$Property->GetIdentifier()];
    }
    
    protected function SetPropertyData(IProperty $Property, $Data) {
        $PropertyIdentifier = $Property->GetIdentifier();
        
        if(!isset($this->Properties[$PropertyIdentifier])) {
            throw new InvalidPropertyException(
                    'The supplied property of entity %s is not part of this %s.',
                    $Property->GetEntityType() ?: '<Undefined>',
                    get_class($this));
        }
        
        $this->Data[$PropertyIdentifier] = $Data;
    }
    
    protected function HasPropertyData(IProperty $Property) {
        return isset($this->Data[$Property->GetIdentifier()]);
    }
    
    protected function RemovePropertyData(IProperty $Property) {
        unset($this->Data[$Property->GetIdentifier()]);
    }
    
    final public function getIterator() {
        return new \ArrayIterator($this->Data);
    }

    final public function offsetExists($Property) {
        return $this->HasPropertyData($Property);
    }
    
    final public function offsetGet($Property) {
        return $this->GetPropertyData($Property);
    }

    final public function offsetSet($Property, $Data) {
        $this->SetPropertyData($Property, $Data);
    }

    final public function offsetUnset($Property) {
        $this->RemovePropertyData($Property);
    }
    
    final public function GetProperty($Identifier) {
        return isset($this->Properties[$Identifier]) ? $this->Properties[$Identifier] : null;
    }
    
    /**
     * Whether or not the property data is the same.
     * 
     * @param PropertyData $Data Another property data
     * @return boolean
     */
    final public function Matches(PropertyData $Data) {
        ksort($this->Data);
        ksort($Data->Data);
        
        return $this->Data === $Data->Data;
    }
}

?>