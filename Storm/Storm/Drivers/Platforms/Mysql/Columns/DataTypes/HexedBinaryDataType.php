<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

class HexedBinaryDataType extends Columns\DataType {
    public function __construct($Length) {
        parent::__construct(
                'BINARY', [$Length], null,
                ParameterType::String);
    }
    
    public function GetReviveExpression(Expression $Expression) {
        return Expression::FunctionCall('HEX', Expression::ValueList([$Expression]));
    }
    
    public function GetPersistExpression(Expression $Expression) {
        return Expression::FunctionCall('UNHEX', Expression::ValueList([$Expression]));
    }
}

?>