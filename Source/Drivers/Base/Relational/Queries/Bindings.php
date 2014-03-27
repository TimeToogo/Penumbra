<?php

namespace Penumbra\Drivers\Base\Relational\Queries;

final class Bindings implements \ArrayAccess {
    private $Bindings = [];
    
    public function offsetExists($ParameterKey) {
        return isset($this->Bindings[$ParameterKey]);
    }

    public function offsetGet($ParameterKey) {
        return $this->Bindings[$ParameterKey];
    }

    public function offsetSet($ParameterKey, $ParameterValue) {
        $this->Bind($ParameterValue, null, $ParameterKey);
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
    
    public function Bind($Value, $ParameterType = null, $ParameterKey = null) {
        $this->DefaultParameterType($ParameterType, $Value);
        if($ParameterKey !== null) {
            $this->Bindings[$ParameterKey] = new Binding($Value, $ParameterType);
        }
        else { 
            $this->Bindings[] = new Binding($Value, $ParameterType);
        }
    }
    
    final public function DefaultParameterType(&$ParameterType, $Value) {
        if ($ParameterType === null) {
            $ParameterType = $this->GetDefaultParameterType($Value);
        }
    }

    private static $ParameterTypeMap = [
        'string' => ParameterType::String,
        'boolean' => ParameterType::Boolean,
        'double' => ParameterType::Double,
        'integer' => ParameterType::Integer,
        'resource' => ParameterType::Stream,
        'NULL' => ParameterType::Null,
    ];
    private function GetDefaultParameterType($Value) {
        $Type = gettype($Value);
        if(isset(self::$ParameterTypeMap[$Type])) {
            return self::$ParameterTypeMap[$Type];
        }
        else {
            return ParameterType::String;
        }
    }
}
final class Binding {
    private $Value;
    private $ParameterType;
    
    public function __construct($Value, $ParameterType) {
        $this->Value = $Value;
        $this->ParameterType = $ParameterType;
    }
    
    public function GetValue() {
        return $this->Value;
    }

    public function GetParameterType() {
        return $this->ParameterType;
    }
}

?>
