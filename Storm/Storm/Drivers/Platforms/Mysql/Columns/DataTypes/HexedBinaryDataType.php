<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class HexedBinaryDataType extends Columns\DataType {
    public function __construct($Length) {
        parent::__construct(
                'BINARY', [$Length], null, 
                'UNHEX(#)', 
                'HEX(#)', 
                ParameterType::String);
    }
}

?>