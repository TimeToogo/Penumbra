<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;

interface IObjectMapper {
    /**
     * @return Expression
     */
    public function MapObjectExpression($Type, $Value);
    
    /**
     * @return Expression
     */
    public function MapMethodCallExpression(CoreExpression $ObjectValueExpression = null, $Type, $Name, array $ArgumentValueExpressions = []);
 
    /**
     * @return CoreExpression
     */
    public function MapPropertyFetchExpression(CoreExpression $ObjectExpression = null, $Type, $Name);
    
}

?>