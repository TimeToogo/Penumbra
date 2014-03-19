<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Drivers\Base\Relational\Expressions\Expression;

interface IExpressionOptimizer {
    /**
     * @return Expression
     */
    public function Optimize(Expression $Expression);
}

?>