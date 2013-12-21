<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

abstract class KeyGenerator implements IKeyGenerator {
    
    public function FillPrimaryKeys(IConnection $Connection, Relational\Table $Table, 
            array $PrimaryKeys, array $PrimaryKeyColumns) {
        if(count($PrimaryKeys) === 0 || count($PrimaryKeyColumns) === 0)
            return;
        else
            return $this->FillAllPrimaryKeys($Connection, $Table, $PrimaryKeys, $PrimaryKeyColumns);
    }
    protected abstract function FillAllPrimaryKeys(IConnection $Connection, Relational\Table $Table, 
            array $PrimaryKeys, array $PrimaryKeyColumns);
}

?>
