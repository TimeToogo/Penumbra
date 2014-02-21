<?php

namespace Storm\Drivers\Base\Relational\Columns;

use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Core\Relational\Expressions\Expression;

class DataType {
    private $DataType;
    private $Parameters;
    private $Extra;
    private $ParameterType;
    
    public function __construct($DataType, array $Parameters = [], $Extra = null, $ParameterType = ParameterType::String) {
        $this->DataType = $DataType;
        $this->Parameters = $Parameters;
        $this->Extra = $Extra;
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

    public function GetReviveExpression(Expression $Expression) {
        return $Expression;
    }
    
    public function GetPersistExpression(Expression $Expression) {
        return $Expression;
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