<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class IntDataType extends Columns\CastingDataType {
    public function __construct($IntType, $Unsigned = false) {
        parent::__construct($IntType, 'int', [], $Unsigned ? 'UNSIGNED' : '', ParameterType::Integer);
    }
}

?>