<?php

namespace Storm\Drivers\Base\Relational\Columns;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class EnumDataType extends Columns\DataType {
    private $ValuesMap;
    public function __construct($DataType, array $ValuesMap, array $Parameters = [], $Extra = null, $ParameterType = ParameterType::String) {
        parent::__construct($DataType, $Parameters, $Extra, $ParameterType);
        
        $this->ValuesMap = $ValuesMap;
    }
    
    public function ToPropertyValue($PersistedValue) {
        if(!isset($this->ValuesMap[$PersistedValue])) {
            throw new DataTypeException(
                    'Cannot map stored enum value to property value: expecting %s, %s given',
                    implode(', ', array_keys($PersistedValue)),
                    \Storm\Utilities\Type::GetTypeOrClass($PersistedValue));
        }
        $PropertyValue = $this->ValuesMap[$PersistedValue];
        
        return $PropertyValue;
    }
    
    public function ToPersistedValue($PropertyValue) {
        $PersistedValue = array_search($PropertyValue, $this->ValuesMap, true);
        if($PersistedValue === false) {
            throw new DataTypeException(
                    'Cannot map enum property value to persisted value: expecting %s, %s given',
                    implode(', ', array_map('strval', $PersistedValue)),
                    \Storm\Utilities\Type::GetTypeOrClass($PropertyValue));
        }
        
        return $PersistedValue;
    }
}

?>