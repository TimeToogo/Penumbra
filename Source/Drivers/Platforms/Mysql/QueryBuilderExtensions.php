<?php

namespace Penumbra\Drivers\Platforms\Mysql;

use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;

final class QueryBuilderExtensions {
     private function __construct() { }
    
    public static function AppendAlterTable(QueryBuilder $Builder, Relational\Table $Table) {
        $Builder->AppendIdentifier('ALTER TABLE # ', [$Table->GetName()]);
    }
}

?>