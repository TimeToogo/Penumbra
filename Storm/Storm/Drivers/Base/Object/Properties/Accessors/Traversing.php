<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object;

class Traversing extends Accessor {
    /**
     * @var Accessor[] 
     */
    private $NestedAccessors = [];
    /**
     * @var Accessor[] 
     */
    private $TraversingAccessors = [];
    /**
     * @var Accessor
     */
    private $FinalAccessor = [];
    
    public function __construct(array $NestedAccessors) {
        if(count($NestedAccessors) === 0) {
            throw new Object\ObjectException('The supplied nested accessors must contain atleast one accessor');
        }
        
        foreach($NestedAccessors as $NestedProperty) {
            $this->Add($NestedProperty);
        }
        $this->TraversingAccessors = $this->NestedAccessors;
        $this->FinalAccessor = array_pop($this->TraversingAccessors);
    }
    
    final protected function GetterIdentifier(&$Identifier) {
        $Identifiers = [];
        foreach($this->NestedAccessors as $NestedAccessor) {
            $Identifiers[] = $NestedAccessor->GetGetterIdentifier();
        }
        
        $Identifier .= implode('->', $Identifiers);
    }
    
    final protected function SetterIdentifier(&$Identifier) {
        $Identifiers = [];
        foreach($this->NestedAccessors as $NestedAccessor) {
            $Identifiers[] = $NestedAccessor->GetSetterIdentifier();
        }
        
        $Identifier .= implode('->', $Identifiers);
    }
    
    public function __clone() {
        foreach($this->NestedAccessors as $Key => $NestedAccessor) {
            $this->NestedAccessors[$Key] = clone $NestedAccessor;
        }
    }
    
    private function Add(Accessor $Accessor) {
        $this->NestedAccessors[] = $Accessor;
    }
    
    /**
     * @return Accessor[]
     */
    final public function GetNestedAccessors() {
        return $this->NestedAccessors;
    }
    
    private function GetTraversedValue($Entity) {
        $Value = $Entity;
        foreach($this->TraversingAccessors as $TraversingAccessor) {
            $TraversingAccessor->SetEntityType(get_class($Value));
            $Value = $TraversingAccessor->GetValue($Value);
        }
        return $Value;
    }
    final public function GetValue($Entity) {
        $Value = $this->GetTraversedValue($Entity);
        $this->FinalAccessor->SetEntityType(get_class($Value));
        
        return $this->FinalAccessor->GetValue($Value);
    }

    final public function SetValue($Entity, $Value) {
        $TraversedValue = $this->GetTraversedValue($Entity);
        $this->FinalAccessor->SetEntityType(get_class($TraversedValue));
                
        $this->FinalAccessor->SetValue($TraversedValue, $Value);
    }
}

?>