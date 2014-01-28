<?php

namespace Storm\Drivers\Platforms\SQLite\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Platforms\Base\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Requests;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Drivers\Base\Relational\PrimaryKeys\ReturningDataKeyGenerator;

class QueryExecutor extends Queries\StandardQueryExecutor {
    
    public function __construct() {
        parent::__construct(new Persister());
    }
}

?>