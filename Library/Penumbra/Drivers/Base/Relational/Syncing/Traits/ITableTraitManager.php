<?php

namespace Penumbra\Drivers\Base\Relational\Syncing\Traits;

use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\TableTrait;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;

interface ITableTraitManager {
    public function AppendAdd(IConnection $Connection, QueryBuilder $QueryBuilder, Relational\Table $Table, TableTrait $Trait);
    
    public function AppendDrop(IConnection $Connection, QueryBuilder $QueryBuilder, Relational\Table $Table, TableTrait $Trait);

    public function AppendDefinition(QueryBuilder $QueryBuilder, Relational\Table $Table, TableTrait $Trait);
}

?>