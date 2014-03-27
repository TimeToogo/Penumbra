<?php

namespace Penumbra\Drivers\Base\Relational\Queries;

use \Penumbra\Drivers\Base\Relational\Expressions\Expression;

interface IExpressionCompiler {
    public function Append(
            QueryBuilder $QueryBuilder, 
            Expression $Expression);
}

?>