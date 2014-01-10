<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

abstract class Accessor {
    private $GlobalIdentifier;
    private $GetterIdentifier;
    private $SetterIdentifier;
    private $EntityType = null;
    
    final public function GetIdentifier() {
        $this->UpdateIdentifer();     
        return $this->GlobalIdentifier;
    }
    final public function GetGetterIdentifier() {
        $this->UpdateIdentifer();     
        return $this->GetterIdentifier;
    }
    final public function GetSetterIdentifier() {
        $this->UpdateIdentifer();     
        return $this->SetterIdentifier;
    }
    
    private function UpdateIdentifer($Force = false) {
        if(!$Force && $this->GlobalIdentifier !== null) {
            return;
        }
        $this->GetterIdentifier = '';
        $this->GetterIdentifier($this->GetterIdentifier);
        $this->SetterIdentifier = '';
        $this->SetterIdentifier($this->SetterIdentifier);
        
        $this->GlobalIdentifier = md5(($this->EntityType ? $this->EntityType : '') . $this->GetterIdentifier . $this->SetterIdentifier);
    }
    protected abstract function GetterIdentifier(&$Identifier);
    protected abstract function SetterIdentifier(&$Identifier);
        
    public function SetEntityType($EntityType) {
        if($EntityType === $this->EntityType) {
            return;
        }
        $this->EntityType = $EntityType;
        $this->UpdateIdentifer(true);        
    }
    
    public abstract function GetValue($Entity);
    public abstract function SetValue($Entity, $PropertyValue);
    
    final public function Is(Accessor $OtherAccessor) {
        return $this->GlobalIdentifier === $OtherAccessor->GlobalIdentifier;
    }
    
    final public function IsGetter(Accessor $OtherAccessor) {
        return $this->GetterIdentifier === $OtherAccessor->SetterIdentifier;
    }
    
    final public function IsSetter(Accessor $OtherAccessor) {
        return $this->SetterIdentifier === $OtherAccessor->SetterIdentifier;
    }
}

?>
