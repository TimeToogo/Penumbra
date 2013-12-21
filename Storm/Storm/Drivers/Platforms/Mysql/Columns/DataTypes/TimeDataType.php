<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class TimeDataType extends Columns\CastingDataType {
    public function __construct() {
        parent::__construct('TIME', 'int', [], '', 
                'SEC_TO_TIME(#)', 
                'TIME_TO_SEC(#)', 
                ParameterType::Integer);
    }
}

?>