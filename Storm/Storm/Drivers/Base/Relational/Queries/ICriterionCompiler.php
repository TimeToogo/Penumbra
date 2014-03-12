<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational\Criteria;

interface ICriteriaCompiler {
    public function AppendTableDefinition(QueryBuilder $QueryBuilder, Criteria $Criteria);
    
    public function AppendWhere(QueryBuilder $QueryBuilder, Criteria $Criteria);
    
    public function AppendOrderBy(QueryBuilder $QueryBuilder, Criteria $Criteria);
    
    public function AppendRange(QueryBuilder $QueryBuilder, Criteria $Criteria);
}

?>