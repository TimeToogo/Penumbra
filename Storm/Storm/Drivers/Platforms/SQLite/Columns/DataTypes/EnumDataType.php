<?php

namespace Storm\Drivers\Platforms\SQLite\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class EnumDataType extends Columns\DataType {
    public function __construct(array $ValuesMap) {
        parent::__construct('ENUM', array_keys($ValuesMap));
    }
    
    
}

?>