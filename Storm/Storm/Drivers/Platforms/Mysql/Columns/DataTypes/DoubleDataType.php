<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class DoubleDataType extends Columns\CastingDataType {
    public function __construct() {
        parent::__construct('DOUBLE', 'double');
    }
}

?>