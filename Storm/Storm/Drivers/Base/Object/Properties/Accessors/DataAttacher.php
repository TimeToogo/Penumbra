<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class DataAttacher extends Accessor {
    private $PropertyKey;
    
    public function __construct($FieldName) {        
        $this->PropertyKey = '__' . $FieldName;
    }
    
    protected function Identifier(&$Identifier) {
        $Identifier .= $this->PropertyKey;
    }
    
    final public function GetValue($Entity) {
        return isset($Entity->{$this->PropertyKey}) ? $Entity->{$this->PropertyKey} : null;
    }

    final public function SetValue($Entity, $Value) {
        $Entity->{$this->PropertyKey} =& $Value;
    }
}

?>
