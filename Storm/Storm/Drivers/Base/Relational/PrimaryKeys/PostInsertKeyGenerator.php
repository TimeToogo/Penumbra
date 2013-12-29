<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Drivers\Base\Relational\Queries\IConnection;

abstract class PostInsertKeyGenerator extends KeyGenerator {
    final public function GetKeyGeneratorMode() {
        return KeyGeneratorMode::PostInsert;
    }
    
    public abstract function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows);
}

?>
