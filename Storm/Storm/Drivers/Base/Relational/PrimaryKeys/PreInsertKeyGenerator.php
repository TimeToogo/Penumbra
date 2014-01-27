<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

abstract class PreInsertKeyGenerator extends KeyGenerator {
    final public function GetKeyGeneratorType() {
        return KeyGeneratorType::PreInsert;
    }
    
    public abstract function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows);
}

?>
