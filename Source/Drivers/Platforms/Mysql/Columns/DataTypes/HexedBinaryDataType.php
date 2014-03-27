<?php

namespace Penumbra\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;
use \Penumbra\Core\Relational\Expression as CoreExpression;
use \Penumbra\Drivers\Base\Relational\Expressions\Expression;

class HexedBinaryDataType extends Columns\FunctionCallDataType {
    public function __construct($Length) {
        parent::__construct(
                'HEX', 'UNHEX',
                'BINARY', [$Length], null,
                ParameterType::String);
    }
}

?>