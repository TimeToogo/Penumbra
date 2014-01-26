<?php

namespace Storm\Drivers\Base\Relational\Queries;

final class Bindings implements \ArrayAccess {
    private $Bindings = array();
    
    public function offsetExists($ParameterKey) {
        return isset($this->Bindings[$ParameterKey]);
    }

    public function offsetGet($ParameterKey) {
        return $this->Bindings[$ParameterKey];
    }

    public function offsetSet($ParameterKey, $ParameterValue) {
        $this->Bindings[$ParameterKey] = new Binding($ParameterValue, ParameterType::String);
    }

    public function offsetUnset($ParameterKey) {
        unset($this->Bindings[$ParameterKey]);
    }
    
    /**
     * @return Binding[]
     */
    public function Get() {
        return $this->Bindings;
    }
    
    public function GetParameterKeys() {
        return array_keys($this->Bindings);
    }
    
    public function Bind(&$Value, $ParameterType, $ParameterKey = null) {
        if($ParameterKey !== null) {
            $this->Bindings[$ParameterKey] = new Binding($Value, $ParameterType);
        }
        else { 
            $this->Bindings[] = new Binding($Value, $ParameterType);
        }
    }
}
final class Binding {
    private $Value;
    private $ParameterType;
    
    public function __construct(&$Value, $ParameterType) {
        $this->Value =& $Value;
        $this->ParameterType = $ParameterType;
    }
    
    public function &GetValue() {
        return $this->Value;
    }

    public function GetParameterType() {
        return $this->ParameterType;
    }
}

?>
