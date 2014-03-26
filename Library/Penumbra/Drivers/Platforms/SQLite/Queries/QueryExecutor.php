<?php

namespace Penumbra\Drivers\Platforms\SQLite\Queries;

use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Table;
use \Penumbra\Drivers\Platforms\Base\Queries;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;
use \Penumbra\Drivers\Base\Relational\Requests;
use \Penumbra\Drivers\Base\Relational\Expressions\Expression;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys\ReturningDataKeyGenerator;

class QueryExecutor extends Queries\StandardQueryExecutor {
    
    public function __construct() {
        parent::__construct(new Persister());
    }
}

?>