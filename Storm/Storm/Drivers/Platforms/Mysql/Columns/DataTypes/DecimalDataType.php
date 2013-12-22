<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class DecimalDataType extends Columns\Column {
    public function __construct($Name, $Length, $Precision, $Unsigned = false) {
        parent::__construct($Name, new Columns\DataType('DECIMAL', [$Length, $Precision], 
                $Unsigned ? 'UNSIGNED' : '', ParameterType::String));
    }
    
    public function ToPersistedValue($PropertyValue) {
        return (double)$PropertyValue;
    }
    
    public function ToPropertyValue($PersistedValue) {
        return (double)$PersistedValue;
    }
}

?>