<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Relational;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

interface IControlFlowMapper {
    
    /**
     * @return R\Expression
     */
    public function MapTernary(
            R\Expression $MappedConditionExpression,
            R\Expression $MappedIfTrueExpression,
            R\Expression $MappedIfFalseExpression);
}

?>