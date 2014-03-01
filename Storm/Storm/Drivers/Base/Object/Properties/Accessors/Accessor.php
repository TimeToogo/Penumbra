<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

abstract class Accessor {
    private $Identifier;
    private $GetterIdentifier;
    private $SetterIdentifier;
    private $EntityType = null;
    
    public function __construct() {
        $this->GetterIdentifier = '';
        $this->GetterIdentifier($this->GetterIdentifier);
        $this->SetterIdentifier = '';
        $this->SetterIdentifier($this->SetterIdentifier);
        
        $this->Identifier = md5($this->GetterIdentifier . $this->SetterIdentifier);
    }
    
    final public function GetIdentifier() {
        return $this->Identifier;
    }
    final public function GetGetterIdentifier() {
        return $this->GetterIdentifier;
    }
    final public function GetSetterIdentifier() {
        return $this->SetterIdentifier;
    }
    
    protected abstract function GetterIdentifier(&$Identifier);
    protected abstract function SetterIdentifier(&$Identifier);
        
    public function SetEntityType($EntityType) {
        $this->EntityType = $EntityType;     
    }
    
    public abstract function GetValue($Entity);
    public abstract function SetValue($Entity, $PropertyValue);
    
    final public function Is(Accessor $OtherAccessor) {
        return $this->Identifier === $OtherAccessor->Identifier;
    }
    
    final public function IsGetter(Accessor $OtherAccessor) {
        return $this->GetterIdentifier === $OtherAccessor->SetterIdentifier;
    }
    
    final public function IsSetter(Accessor $OtherAccessor) {
        return $this->SetterIdentifier === $OtherAccessor->SetterIdentifier;
    }
}

?>
