<?php

namespace Storm\Drivers\Platforms\Base\Queries;

interface ICriteriaCompiler {
    public function AppendWhere(QueryBuilder $QueryBuilder, array $Expressions);
    
    public function AppendOrderBy(QueryBuilder $QueryBuilder, array $Expressions);
    
    public function AppendRange(QueryBuilder $QueryBuilder, $RangeStart, $RangeLimit);
}

?>