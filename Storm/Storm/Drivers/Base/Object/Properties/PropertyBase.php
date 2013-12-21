<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object\IProperty;

abstract class PropertyBase implements IProperty {
    protected $Name;
    protected $IsIdentity;
    
    public function __construct(
            $Name,
            $IsIdentity = false) {
        $this->Name = $Name;
        $this->IsIdentity = $IsIdentity;
    }

    final public function GetName() {
        return $this->Name;
    }

    final public function IsIdentity() {
        return $this->IsIdentity;
    }
}

?>
