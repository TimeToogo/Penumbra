<?php

namespace Penumbra\Drivers\Platforms\Base\Queries;

use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;

interface ICriteriaCompiler {
    public function AppendWhere(QueryBuilder $QueryBuilder, array $Expressions);
    
    public function AppendOrderBy(QueryBuilder $QueryBuilder, \SplObjectStorage $ExpressionAscendingMap);
    
    public function AppendRange(QueryBuilder $QueryBuilder, $RangeStart, $RangeLimit);
}

?>