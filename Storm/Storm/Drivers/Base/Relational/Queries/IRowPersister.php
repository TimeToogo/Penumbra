<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;

interface IRowPersister {
    public function PersistRows(
            IConnection $Connection, 
            Relational\ITable $Table, 
            array $RowsToPersist);
    
    public function DeleteRows(
            IConnection $Connection, 
            Relational\ITable $Table, 
            array $PrimaryKeys);
}

?>