<?php

namespace Storm\Drivers\Base\Relational\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class EnumDataType extends Columns\DataType {
    private $ValuesMap;
    public function __construct($DataType, array $ValuesMap, array $Parameters = array(), $Extra = null, $ParameterType = ParameterType::String) {
        parent::__construct($DataType, $Parameters, $Extra, $ParameterType);
    }
    
    public function ToPropertyValue($PersistedValue) {
        if(!isset($this->ValuesMap[$PersistedValue])) {
            throw new \Exception;
        }
        $PropertyValue = $this->ValuesMap[$PersistedValue];
        
        return $PropertyValue;
    }
    public function ToPersistedValue($PropertyValue) {
        $PersistedValue = array_search($PropertyValue, $this->ValuesMap);
        if($PersistedValue === false) {
            throw new \Exception;
        }
        
        return $PersistedValue;
    }
}

?>