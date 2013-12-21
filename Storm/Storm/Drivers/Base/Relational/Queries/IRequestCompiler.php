<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational\Request;
use \Storm\Core\Relational\Operation;

interface IRequestCompiler {
    public function AppendRequest(QueryBuilder $QueryBuilder, Request $Request);
    public function AppendOperation(QueryBuilder $QueryBuilder, Operation $Operation);
}

?>