<?php

namespace Storm\Drivers\Platforms\Null;

use \Storm\Drivers\Base\Relational;

final class NullQueryExecutor implements Relational\Queries\IQueryExecutor {
    public function Select(Relational\Queries\IConnection $Connection, \Storm\Core\Relational\Request $Request) { }
    public function Commit(Relational\Queries\IConnection $Connection, \Storm\Core\Relational\Transaction $Transaction) { }
}

?>