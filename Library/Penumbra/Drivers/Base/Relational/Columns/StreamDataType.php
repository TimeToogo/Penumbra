<?php

namespace Penumbra\Drivers\Base\Relational\Columns;

use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;

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