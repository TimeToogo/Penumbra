<?php

namespace Penumbra\Drivers\Base\Relational\PrimaryKeys;

use \Penumbra\Drivers\Base\Relational\Queries\IConnection;

abstract class PostMultiInsertKeyGenerator extends KeyGenerator {
    final public function GetKeyGeneratorType() {
        return KeyGeneratorType::PostMultiInsert;
    }
    
    public abstract function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows);
}

?>
