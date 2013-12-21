<?php

namespace Storm\Drivers\Platforms\Mysql;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

final class QueryBuilderExtensions {
     private function __construct() { }
    
    public static function AppendAlterTable(QueryBuilder $Builder, Relational\Table $Table) {
        $Builder->AppendIdentifier('ALTER TABLE # ', [$Table->GetName()]);
    }
}

?>