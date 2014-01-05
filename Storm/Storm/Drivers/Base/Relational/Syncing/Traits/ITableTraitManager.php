<?php

namespace Storm\Drivers\Base\Relational\Syncing\Traits;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\TableTrait;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

interface ITableTraitManager {
    public function AppendAdd(IConnection $Connection, QueryBuilder $QueryBuilder, Relational\Table $Table, TableTrait $Trait);
    
    public function AppendDrop(IConnection $Connection, QueryBuilder $QueryBuilder, Relational\Table $Table, TableTrait $Trait);

    public function AppendDefinition(QueryBuilder $QueryBuilder, Relational\Table $Table, TableTrait $Trait);
}

?>