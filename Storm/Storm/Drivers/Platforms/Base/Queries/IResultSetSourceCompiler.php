<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

interface IResultSetSourceCompiler {
    public function AppendResultSetSources(
            QueryBuilder $QueryBuilder, 
            Relational\ResultSetSources $Sources,
            \SplObjectStorage $SourceAliasMap);
}

?>