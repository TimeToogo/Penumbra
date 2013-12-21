<?php

namespace Storm\Drivers\Base\Relational\Columns;

use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class DataType {
    private $DataType;
    private $Parameters;
    private $Extra;
    private $PersistFormat;
    private $ReviveFormat;
    private $ParameterType;
    
    public function __construct($DataType, array $Parameters = array(), $Extra = null,
            $PersistFormat = '#', $ReviveFormat = '#', $ParameterType = ParameterType::String) {
        $this->DataType = $DataType;
        $this->Parameters = $Parameters;
        $this->Extra = $Extra;
        $this->PersistFormat = $PersistFormat;
        $this->ReviveFormat = $ReviveFormat;
        $this->ParameterType = $ParameterType;
    }
    
    final public function GetDataType() {
        return $this->DataType;
    }

    final public function GetParameters() {
        return $this->Parameters;
    }

    final public function GetExtra() {
        return $this->Extra;
    }
        
    final public function Is(DataType $OtherDataType) {
        if(strtoupper($this->DataType) !== strtoupper($OtherDataType->DataType))
            return false;
        if($this->Extra !== $OtherDataType->Extra)
            return false;
        else
            return $OtherDataType->Parameters === $OtherDataType->Parameters;
    }
    
    final public function GetPersistFormat() {
        return $this->PersistFormat;
    }

    final public function GetReviveFormat() {
        return $this->ReviveFormat;
    }
    
    final public function GetParameterType() {
        return $this->ParameterType;
    }
    
    public function ToPropertyValue($PersistedValue) {
        return $PersistedValue;
    }
    
    public function ToPersistedValue($PropertyValue) {
        return $PropertyValue;
    }
}
?>