<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Core\Relational\Row;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

abstract class PostIndividualInsertKeyGenerator extends KeyGenerator {
    final public function GetKeyGeneratorType() {
        return KeyGeneratorType::PostMultiInsert;
    }
    
    public abstract function FillPrimaryKey(IConnection $Connection, Row $UnkeyedRow);
}

?>
