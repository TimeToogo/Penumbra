<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

class HexedBinaryDataType extends Columns\FunctionCallDataType {
    public function __construct($Length) {
        parent::__construct(
                'HEX', 'UNHEX',
                'BINARY', [$Length], null,
                ParameterType::String);
    }
}

?>