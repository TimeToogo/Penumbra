<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational;

interface IFunctionMapper {
    /**
     * @return Expression
     */
    public function MapFunctionCall(
            $FunctionName, 
            array $MappedArgumentExpressions,
            O\TraversalExpression $FunctionTraversalExpression = null);
}

?>