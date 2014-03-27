<?php

namespace Penumbra\Drivers\Platforms\Mysql\Columns\DataTypes;

use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;
use \Penumbra\Core\Relational\Expression as CoreExpression;
use \Penumbra\Drivers\Base\Relational\Expressions\Expression;

class TimeDataType extends Columns\FunctionCallDataType {
    public function __construct() {
        parent::__construct(
                'TIME_TO_SEC', 'SEC_TO_TIME',
                'TIME', [], null, 
                ParameterType::Integer);
    }
    
    public function GetReviveExpression(CoreExpression $Expression) {
        return Expression::FunctionCall('TIME_TO_SEC', [$Expression]);
    }
    
    public function GetPersistExpression(CoreExpression $Expression) {
        return Expression::FunctionCall('SEC_TO_TIME', [$Expression]);
    }
}

?>