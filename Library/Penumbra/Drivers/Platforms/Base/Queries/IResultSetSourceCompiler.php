<?php

namespace Penumbra\Drivers\Platforms\Base\Queries;

use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Queries;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;
use \Penumbra\Drivers\Base\Relational\Expressions\Expression;

interface IResultSetSourceCompiler {
    public function AppendResultSetSources(
            QueryBuilder $QueryBuilder, 
            Relational\ResultSetSources $Sources,
            ColumnResolverWalker $ColumnResolverWalker);
}

?>