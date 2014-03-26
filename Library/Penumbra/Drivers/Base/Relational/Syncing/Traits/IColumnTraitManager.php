<?php

namespace Penumbra\Drivers\Base\Relational\Syncing\Traits;

use \Penumbra\Drivers\Base\Relational\Columns\ColumnTrait;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;

interface IColumnTraitManager {
    public function AppendDefinition(QueryBuilder $QueryBuilder, ColumnTrait $Trait);
}

?>