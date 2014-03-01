<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class GetterSetter extends Accessor {
    private $PropertyGetter;
    private $PropertySetter;
    
    public function __construct(
            IPropertyGetter $PropertyGetter, 
            IPropertySetter $PropertySetter) {
        $this->PropertyGetter = $PropertyGetter;
        $this->PropertySetter = $PropertySetter;
        parent::__construct();
    }
    
    protected function GetterIdentifier(&$Identifier) {
        $this->PropertyGetter->Identifier($Identifier);
    }
    
    protected function SetterIdentifier(&$Identifier) {
        $this->PropertySetter->Identifier($Identifier);
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
    
    public function SetEntityType($EntityType) {
        parent::SetEntityType($EntityType);
        $this->PropertyGetter->SetEntityType($EntityType);
        $this->PropertySetter->SetEntityType($EntityType);
    }

    final public function GetValue($Entity) {
        return $this->PropertyGetter->GetValueFrom($Entity);
    }

    final public function SetValue($Entity, $Value) {
        $this->PropertySetter->SetValueTo($Entity, $Value);
    }
}

?>
