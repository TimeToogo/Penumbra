<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational\Expressions\Expression;

interface IExpressionCompiler {
    public function Append(QueryBuilder $QueryBuilder, Expression $Expression);
}

?>