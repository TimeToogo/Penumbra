<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object\IProperty;

class TraversedObjectProperty implements IProperty {
    private $NestedGetterProperties = array();
    private $FinalProperty;
    
    public function __construct(array $NestedGetterProperties, IProperty $FinalProperty) {
        if(count($NestedGetterProperties) === 0)
            throw new \InvalidArgumentException('$NestedGetterProperties must not be empty');
        
        foreach($NestedGetterProperties as $NestedProperty) {
            $this->Add($NestedProperty);
        }
        $this->SetFinalProperty($FinalProperty);
    }
    
    /**
     * @return IProperty[]
     */
    final public function GetNestedGetterProperties() {
        return $this->NestedGetterProperties;
    }

    /**
     * @return IProperty
     */
    final public function GetFinalProperty() {
        return $this->FinalProperty;
    }

    public function GetName() {
        return $this->FinalProperty->GetName();
    }

    public function IsIdentity() {
        $this->FinalProperty->IsIdentity();
    }
    
    final public function Add(IProperty $Property) {
        if(!$Property->CanGetValue())
            throw new \InvalidArgumentException('$Property must not be empty');
        
        $this->NestedGetterProperties[] = $Property;
    }
    final public function SetFinalProperty(IProperty $FinalProperty) {
        $this->FinalProperty = $FinalProperty;
    }

    final public function ValidPropertyOf($EntityType) {
        return $this->NestedGetterProperties[0]->ValidPropertyOf($EntityType);
    }
    
    final public function CanGetValue() {
        return $this->FinalProperty->CanGetValue();
    }

    final public function CanSetValue() {
        return $this->FinalProperty->CanSetValue();
    }
    
    private function &GetTraversedValue($Entity) {
        $Value = $Entity;
        foreach($this->NestedGetterProperties as $NestedProperty) {
            $Value =& $NestedProperty->GetValue($Value);
        }
        return $Value;
    }
    final public function &GetValue($Entity) {
        $TraversedValue =& $this->GetTraversedValue($Entity);
        
        return $this->FinalProperty->GetValue($TraversedValue);
    }

    final public function SetValue($Entity, &$Value) {
        $TraversedValue =& $this->GetTraversedValue($Entity);
        
        $this->FinalProperty->SetValueTo($TraversedValue, $Value);
    }
}

?>