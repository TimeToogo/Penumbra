<?php

namespace Penumbra\Drivers\Base\Mapping\Expressions;

use \Penumbra\Core\Relational;
use \Penumbra\Core\Object\Expressions as O;
use \Penumbra\Drivers\Base\Relational\Expressions as R;

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