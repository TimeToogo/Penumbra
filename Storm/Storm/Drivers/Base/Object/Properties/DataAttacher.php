<?php

namespace Storm\Drivers\Base\Object\Properties;

class DataAttacher extends PropertyBase {
    private $PropertyKey;
    
    public function __construct($IsIdentity, $Key, $Name = null) {
        parent::__construct($IsIdentity, $Name === null ? $Key : $Name);
        
        $this->PropertyKey = '__' . $Key;
    }
    
    final protected function ValidPropertyOf($EntityType) {
        return true;
    }

    final public function CanGetValue() {
        return true;
    }
    
    final public function CanSetValue() {
        return true;
    }
    
    final public function &GetValue($Entity) {
        return isset($Entity->{$this->PropertyKey}) ? $Entity->{$this->PropertyKey} : null;
    }

    final public function SetValue($Entity, &$Value) {
        $Entity->{$this->PropertyKey} =& $Value;
    }
}

?>
