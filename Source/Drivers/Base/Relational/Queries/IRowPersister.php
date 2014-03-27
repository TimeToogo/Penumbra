<?php

namespace Penumbra\Drivers\Base\Relational\Queries;

use \Penumbra\Core\Relational;

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