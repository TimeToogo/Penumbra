<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational\Constraints\Predicate;

interface IPredicateCompiler {
    public function Append(QueryBuilder $QueryBuilder, Predicate $Predicate);
}

?>