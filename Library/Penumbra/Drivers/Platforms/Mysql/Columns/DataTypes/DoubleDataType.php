<?php

namespace Penumbra\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;

class DoubleDataType extends Columns\CastingDataType {
    public function __construct() {
        parent::__construct('DOUBLE', 'double');
    }
}

?>