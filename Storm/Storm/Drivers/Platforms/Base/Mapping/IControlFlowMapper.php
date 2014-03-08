<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Relational;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational\Expression;

interface IControlFlowMapper {
    
    public function MapTernary(
            Expression $MappedConditionExpression,
            Expression $MappedIfTrueExpression,
            Expression $MappedIfFalseExpression);
}

?>