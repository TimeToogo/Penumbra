<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational\Expressions\Expression;

interface IExpressionOptimizer {
    /**
     * @return Expression
     */
    public function Optimize(Expression $Expression);
}

?>