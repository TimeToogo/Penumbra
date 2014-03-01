<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational\Expression;

interface IExpressionOptimizer {
    /**
     * @return Expression
     */
    public function Optimize(Expression $Expression);
}

?>