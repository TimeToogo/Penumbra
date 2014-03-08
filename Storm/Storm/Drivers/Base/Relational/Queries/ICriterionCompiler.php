<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational\Criterion;

interface ICriterionCompiler {
    public function AppendTableDefinition(QueryBuilder $QueryBuilder, Criterion $Criterion);
    
    public function AppendWhere(QueryBuilder $QueryBuilder, Criterion $Criterion);
    
    public function AppendOrderBy(QueryBuilder $QueryBuilder, Criterion $Criterion);
    
    public function AppendRange(QueryBuilder $QueryBuilder, Criterion $Criterion);
}

?>