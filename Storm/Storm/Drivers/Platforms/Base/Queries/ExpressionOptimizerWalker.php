<?php

 namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Drivers\Base\Relational\Expressions as R;

abstract class ExpressionOptimizerWalker extends R\ExpressionWalker implements IExpressionOptimizer {
    
    public function Optimize(R\Expression $Expression) {
        return $this->Walk($Expression);
    }
}

?>