<?php

namespace Storm\Drivers\Base\Relational\Columns;

use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Drivers\Base\Mapping\Expressions;

abstract class ObjectDataType extends DataType {
    
    private $ClassType;
    
    public function __construct($DataType, array $Parameters = [], $Extra = null, $ParameterType = ParameterType::String) {
        parent::__construct($DataType, $Parameters, $Extra, $ParameterType);
        
        $this->ClassType = $this->ClassType();
    }
    protected abstract function ClassType();
    
    final public function GetClassType() {
        return $this->ClassType;
    }
    
    final public function ToPersistedValue($PropertyValue) {
        if($PropertyValue === null) {
            return null;
        }
        if(!($PropertyValue instanceof $this->ClassType)) {
            throw new DataTypeException(
                    'Invalid property value for %s: expecting %s, %s given',
                    get_class($this),
                    $this->ClassType,
                    \Storm\Core\Utilities::GetTypeOrClass($PropertyValue));
        }
        
        return $this->PersistedValue($PropertyValue);
    }
    protected abstract function PersistedValue($PropertyValue);
    
    final public function ToPropertyValue($PersistedValue) {
        if($PersistedValue === null) {
            return null;
        }
        return $this->PropertyValue($PersistedValue);
    }
    protected abstract function PropertyValue($PersistedValue);
    
}
?>