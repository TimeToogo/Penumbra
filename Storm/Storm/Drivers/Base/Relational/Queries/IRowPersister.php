<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational\Select;

interface IRowPersister {
    public function PersistRows(
            IConnection $Connection, 
            Table $Table, 
            array $RowsToPersist);
    
    public function DeleteRows(
            IConnection $Connection, 
            Table $Table, 
            array $PrimaryKeys);
}

?>