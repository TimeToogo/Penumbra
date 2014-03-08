<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;

interface IQueryCompiler {
    public function AppendSelect(QueryBuilder $QueryBuilder, Relational\Select $Select);
    public function AppendUpdate(QueryBuilder $QueryBuilder, Relational\Update $Update);
    public function AppendDelete(QueryBuilder $QueryBuilder, Relational\Delete $Delete);
}

?>