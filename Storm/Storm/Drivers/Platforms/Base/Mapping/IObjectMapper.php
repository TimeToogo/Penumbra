<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational;
use \Storm\Core\Relational\Expression as CoreExpression;

interface IObjectMapper {    
    /**
     * @return Expression
     */
    public function MapInstance($Instance, O\TraversalExpression $TraversalExpression = null);
    
    /**
     * @return Expression
     */
    public function MapNew($ClassType, array $MappedArgumentExpressions, O\TraversalExpression $TraversalExpression = null);
    
    
}

?>