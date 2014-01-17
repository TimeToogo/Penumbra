<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class DataAttacher extends Accessor {
    private $PropertyKey;
    
    public function __construct($FieldName) {        
        $this->PropertyKey = '__' . $FieldName;
    }

    protected function GetterIdentifier(&$Identifier) {
        $Identifier .= $this->PropertyKey;
    }

    protected function SetterIdentifier(&$Identifier) {
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
