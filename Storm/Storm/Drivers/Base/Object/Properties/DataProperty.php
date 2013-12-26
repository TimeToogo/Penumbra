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
        $this->GetAccessor()->SetValue($Entity, $PropertyValue);
    }
    
    public function GetValue($Entity) {
        return $this->GetAccessor()->GetValue($Entity);
    }
}

?>
