<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

class TimeDataType extends Columns\CastingDataType {
    public function __construct() {
        parent::__construct('TIME', 'int', [], '', 
                ParameterType::Integer);
    }
    
    public function GetReviveExpression(CoreExpression $Expression) {
        return Expression::FunctionCall('TIME_TO_SEC', Expression::ValueList([$Expression]));
    }
    
    public function GetPersistExpression(CoreExpression $Expression) {
        return Expression::FunctionCall('SEC_TO_TIME', Expression::ValueList([$Expression]));
    }
}

?>