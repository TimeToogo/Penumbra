<?php

namespace Penumbra\Drivers\Platforms\Mysql\PrimaryKeys;

use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;

/**
 * Note: this is only safe when on innodb when 'innodb_autoinc_lock_mode' is equal to 0 or 1
 * as this ensures that when a multi row insert is done, the auto increments
 * are guaranteed to be sequential.
 */
class MultiAutoIncrementGenerator extends PrimaryKeys\PostMultiInsertKeyGenerator {
    use \Penumbra\Drivers\Platforms\Standard\PrimaryKeys\AutoIncrementColumnGenerator;
    
    
    public function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows) {
        //Mysql will return the first auto increment from a multi insert
        $FirstInsertId = (int)$Connection->GetLastInsertIncrement();
        $IncrementId = $FirstInsertId;
        foreach ($UnkeyedRows as $Row) {
            $Row[$this->IncrementColumn] = $this->IncrementColumn->ToPersistenceValue($IncrementId);
            $IncrementId++;
        }
    }
}

?>
