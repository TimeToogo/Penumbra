<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;

interface IQueryExecutor {
    /**
     * @return Row[]
     */
    public function Select(IConnection $Connection, Relational\Request $Request);
    
    public function Commit(IConnection $Connection, 
            array $TablesOrderedByPersistingDependency,
            array $TablesOrderedByDiscardingDependency,
            Relational\Transaction $Transaction);
}

?>