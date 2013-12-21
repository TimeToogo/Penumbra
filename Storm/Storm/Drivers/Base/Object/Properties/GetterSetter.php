<?php

namespace Storm\Drivers\Base\Object\Properties;

class GetterSetter extends PropertyBase {
    private $PropertyGetter;
    private $PropertySetter;
    
    public function __construct(
            $Name,
            $IsIdentity,
            IPropertyGetter $PropertyGetter = null, 
            IPropertySetter $PropertySetter = null) {
        parent::__construct($Name, $IsIdentity);
        
        $this->PropertyGetter = $PropertyGetter;
        $this->PropertySetter = $PropertySetter;
    }
    
    public function ValidPropertyOf($EntityType) {
        if($this->CanGetValue()) {
            if(!$this->PropertyGetter->CanGetValueFrom($EntityType))
                return false;
        }
        if($this->CanSetValue()) {
            if(!$this->PropertySetter->CanSetValueTo($EntityType))
                return false;
        }
        return true;
    }
    
    /**
     * @return IPropertyGetter
     */
    public function GetPropertyGetter() {
        return $this->PropertyGetter;
    }

    /**
     * @return IPropertySetter
     */
    public function GetPropertySetter() {
        return $this->PropertySetter;
    }
    
    public function CanGetValue() {
        return $this->PropertyGetter !== null;
    }
    
    public function CanSetValue() {
        return $this->PropertySetter !== null;
    }

    public function &GetValue($Entity) {
        return $this->PropertyGetter->GetValueFrom($Entity);
    }

    public function SetValue($Entity, &$Value) {
        $this->PropertySetter->SetValueTo($Entity, $Value);
    }
}

?>
