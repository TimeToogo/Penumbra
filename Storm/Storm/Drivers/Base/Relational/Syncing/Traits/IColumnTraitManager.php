<?php

namespace Storm\Drivers\Base\Relational\Syncing\Traits;

use \Storm\Drivers\Base\Relational\Columns\ColumnTrait;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

interface IColumnTraitManager {
    public function AppendDefinition(QueryBuilder $QueryBuilder, ColumnTrait $Trait);
}

?>