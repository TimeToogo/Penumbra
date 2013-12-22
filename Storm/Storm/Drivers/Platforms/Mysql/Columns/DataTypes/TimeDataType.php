<?php

namespace Storm\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;

class TimeDataType extends Columns\CastingDataType {
    public function __construct() {
        parent::__construct('TIME', 'int', [], '', 
                ParameterType::Integer);
    }
    
    public function GetReviveExpression(Expression $Expression) {
        return Expression::FunctionCall('TIME_TO_SEC', Expression::ValueList([$Expression]));
    }
    
    public function GetPersistExpression(Expression $Expression) {
        return Expression::FunctionCall('SEC_TO_TIME', Expression::ValueList([$Expression]));
    }
}

?>