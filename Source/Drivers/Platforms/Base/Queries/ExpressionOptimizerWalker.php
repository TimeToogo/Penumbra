<?php

 namespace Penumbra\Drivers\Platforms\Base\Queries;

use \Penumbra\Drivers\Base\Relational\Expressions as R;

abstract class ExpressionOptimizerWalker extends R\ExpressionWalker implements IExpressionOptimizer {
    
    public function Optimize(R\Expression $Expression) {
        return $this->Walk($Expression);
    }
}

?>