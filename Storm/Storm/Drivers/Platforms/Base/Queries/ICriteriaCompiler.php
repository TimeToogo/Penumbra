<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

interface ICriteriaCompiler {
    public function AppendWhere(QueryBuilder $QueryBuilder, array $Expressions);
    
    public function AppendOrderBy(QueryBuilder $QueryBuilder, \SplObjectStorage $ExpressionAscendingMap);
    
    public function AppendRange(QueryBuilder $QueryBuilder, $RangeStart, $RangeLimit);
}

?>