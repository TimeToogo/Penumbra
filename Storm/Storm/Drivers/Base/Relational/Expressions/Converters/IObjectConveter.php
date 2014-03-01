<?php

namespace Storm\Drivers\Base\Relational\Expressions\Converters;

use \Storm\Core\Relational;
use \Storm\Core\Relational\Expression as CoreExpression;

interface IObjectConveter {
    /**
     * @return Expression
     */
    public function MapNewExpression($Type, array $ArgumentValueExpressions = []);
    
    /**
     * @return Expression
     */
    public function MapObjectExpression($Type, $Value);
    
    /**
     * @return Expression
     */
    public function MapMethodCallExpression(ObjectO $ObjectValueExpression = null, $Type, $Name, array $ArgumentValueExpressions = []);
 
    /**
     * @return CoreExpression
     */
    public function MapPropertyFetchExpression(CoreExpression $ObjectExpression = null, $Type, $Name);
 
    /**
     * @return CoreExpression
     */
    public function MapIndexExpression(CoreExpression $ObjectExpression = null, $Type, $Name);
    
}

?>