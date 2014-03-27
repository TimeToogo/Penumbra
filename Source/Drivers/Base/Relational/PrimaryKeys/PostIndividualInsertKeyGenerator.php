<?php

namespace Penumbra\Drivers\Base\Relational\PrimaryKeys;

use \Penumbra\Core\Relational\Row;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;

abstract class PostIndividualInsertKeyGenerator extends KeyGenerator {
    final public function GetKeyGeneratorType() {
        return KeyGeneratorType::PostIndividualInsert;
    }
    
    public abstract function FillPrimaryKey(IConnection $Connection, Row $UnkeyedRow);
}

?>
