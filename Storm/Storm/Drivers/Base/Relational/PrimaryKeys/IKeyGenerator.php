<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

interface IKeyGenerator {
    /**
     * @return Relational\PrimaryKey[]
     */
    public function FillPrimaryKeys(IConnection $Connection, Relational\Table $Table, array $PrimaryKeys, array $PrimaryKeyColumns);
}

?>
