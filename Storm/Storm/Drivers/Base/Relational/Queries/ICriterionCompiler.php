<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational\Criterion;

interface ICriterionCompiler {
    public function AppendCriterion(QueryBuilder $QueryBuilder, Criterion $Criterion);
}

?>