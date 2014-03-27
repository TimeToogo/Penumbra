<?php

namespace Penumbra\Drivers\Platforms\Base\Queries;

use \Penumbra\Drivers\Base\Relational\Expressions\Expression;

interface IExpressionOptimizer {
    /**
     * @return Expression
     */
    public function Optimize(Expression $Expression);
}

?>