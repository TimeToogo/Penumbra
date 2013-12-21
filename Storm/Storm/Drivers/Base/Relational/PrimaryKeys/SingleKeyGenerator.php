<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Columns\Column;

abstract class SingleKeyGenerator extends KeyGenerator { 
    final public function FillAllPrimaryKeys(IConnection $Connection, Relational\Table $Table, 
            array $PrimaryKeys, array $PrimaryKeyColumns) {
        if(count($PrimaryKeyColumns) !== 1)
            throw new \InvalidArgumentException('Can only generate a single primary key');
        
        $this->FillSinglePrimaryKeys($Connection, $Table, $PrimaryKeys, $Column);
    }
    protected abstract function FillSinglePrimaryKeys(IConnection $Connection, Relational\Table $Table, 
            array $PrimaryKeys, Column $Column);
}

?>
