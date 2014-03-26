<?php

namespace Penumbra\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;

class IntDataType extends Columns\CastingDataType {
    public function __construct($IntType, $Extra = null) {
        parent::__construct($IntType, 'int', [], $Extra, ParameterType::Integer);
    }
}

?>