<?php

namespace Penumbra\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;

class BooleanBitDataType extends Columns\DataType {
    public function __construct() {
        parent::__construct('BIT', [1]);
    }
    
    public function ToPersistedValue($PropertyValue) {
        return $PropertyValue ? 1 : 0;
    }
    
    public function ToPropertyValue($PersistedValue) {
        return (bool)$PersistedValue;
    }
}

?>