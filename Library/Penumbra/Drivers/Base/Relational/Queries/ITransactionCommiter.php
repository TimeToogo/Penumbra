<?php

namespace Penumbra\Drivers\Base\Relational\Queries;

use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Table;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys;

interface ITransactionCommiter {
    
    public function Commit(IConnection $Connection, 
            array $TablesOrderedByPersistingDependency, 
            array $TablesOrderedByDiscardingDependency, 
            Relational\Transaction $Transaction);
}

?>