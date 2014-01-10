<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class Traversing extends Accessor {
    /**
     * @var Accessor[] 
     */
    private $NestedAccessors = array();
    /**
     * @var Accessor[] 
     */
    private $TraversingAccessors = array();
    /**
     * @var Accessor
     */
    private $FinalAccessor = array();
    
    public function __construct(array $NestedAccessors) {
        if(count($NestedAccessors) === 0) {
            throw new \InvalidArgumentException('$NestedGetterProperties must not be empty');
        }
        
        foreach($NestedAccessors as $NestedProperty) {
            $this->Add($NestedProperty);
        }
        $this->TraversingAccessors = $this->NestedAccessors;
        $this->FinalAccessor = array_pop($this->TraversingAccessors);
    }
    
    final protected function GetterIdentifier(&$Identifier) {
        foreach($this->NestedAccessors as $NestedAccessor) {
            $NestedAccessor->GetterIdentifier($Identifier);
        }
    }
    
    final protected function SetterIdentifier(&$Identifier) {
        foreach($this->NestedAccessors as $NestedAccessor) {
            $NestedAccessor->SetterIdentifier($Identifier);
        }
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
    
    private function &GetTraversedValue($Entity) {
        $Value = $Entity;
        foreach($this->TraversingAccessors as $TraversingAccessor) {
            $TraversingAccessor->SetEntityType(get_class($Value));
            $Value =& $TraversingAccessor->GetValue($Value);
        }
        return $Value;
    }
    final public function GetValue($Entity) {
        $Value =& $this->GetTraversedValue($Entity);
        $this->FinalAccessor->SetEntityType(get_class($Value));
        
        return $this->FinalAccessor->GetValue($Value);
    }

    final public function SetValue($Entity, $Value) {
        $TraversedValue =& $this->GetTraversedValue($Entity);
        $this->FinalAccessor->SetEntityType(get_class($TraversedValue));
                
        $this->FinalAccessor->SetValue($TraversedValue, $Value);
    }
}

?>