<?php

namespace Storm\Drivers\Base\Relational\Columns;

use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class StreamDataType extends DataType {
    public function __construct($DataType, array $Parameters = [], $Extra = null){
        parent::__construct($DataType, $Parameters, $Extra, ParameterType::Stream);
    }
    
    public function ToPersistedValue($PropertyValue) {
        return $PropertyValue;
    }
    
    public function ToPropertyValue($PersistedValue) {
        return $PersistedValue;
    }
}

?>