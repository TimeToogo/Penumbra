<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational;
use \Storm\Core\Relational\Expressions\Expression;

interface IObjectMapper {
    /**
     * @return Expression
     */
    public function MapObjectExpression($Type, $Value);
    
    /**
     * @return Expression
     */
    public function MapMethodCallExpression(Expression $ObjectValueExpression = null, $Type, $Name, array $ArgumentValueExpressions = array());
}

?>