<?php

namespace Penumbra\Drivers\Base\Relational\PrimaryKeys;

use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;

abstract class PreInsertKeyGenerator extends KeyGenerator {
    final public function GetKeyGeneratorType() {
        return KeyGeneratorType::PreInsert;
    }
    
    public abstract function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows);
}

?>
