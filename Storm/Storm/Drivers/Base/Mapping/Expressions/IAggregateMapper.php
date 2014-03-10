<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational;

interface IAggregateMapper {
    /**
     * @return Expression
     */
    public function MapAggregateTraversal(
            O\TraversalExpression $FunctionTraversalExpression,
            array $MappedArgumentExpressions);
}

?>