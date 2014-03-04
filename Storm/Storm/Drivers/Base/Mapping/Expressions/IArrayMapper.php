<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Relational;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational\Expression;

interface IArrayMapper {
    public function MapArray(array $Array);
    
    public function MapArrayExpression(
            array $MappedKeyExpressions, 
            array $MappedValueExpressions,
            O\TraversalExpression $TraversalExpression = null);
}

?>