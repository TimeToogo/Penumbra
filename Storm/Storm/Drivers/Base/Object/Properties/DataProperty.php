<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;

class DataProperty extends Property implements Object\IDataProperty {
    private $IsIdentity;
    public function __construct(Accessors\Accessor $Accessor, $IsIdentity = false) {
        parent::__construct($Accessor);
        $this->IsIdentity = $IsIdentity;
    }
    
    final public function IsIdentity() {
        return $this->IsIdentity;
    }
    
    
    public function ReviveValue($PropertyValue, $Entity) {
        $this->Accessor->SetValue($Entity, $PropertyValue);
    }
    
    public function GetValue($Entity) {
        return $this->Accessor->GetValue($Entity);
    }

    protected function UpdateAccessor(Accessors\Accessor $Accessor) {
        return new self($Accessor, $this->IsIdentity);
    }
}

?>