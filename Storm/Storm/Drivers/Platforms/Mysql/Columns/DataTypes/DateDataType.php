<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class DateDataType extends TimestampedDataType {
    public function __construct() {
        parent::__construct('DATE');
    }
}

?>