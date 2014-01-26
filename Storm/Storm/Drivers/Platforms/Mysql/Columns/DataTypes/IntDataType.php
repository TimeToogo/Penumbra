<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class IntDataType extends Columns\CastingDataType {
    public function __construct($IntType, $Extra = null) {
        parent::__construct($IntType, 'int', [], $Extra, ParameterType::Integer);
    }
}

?>