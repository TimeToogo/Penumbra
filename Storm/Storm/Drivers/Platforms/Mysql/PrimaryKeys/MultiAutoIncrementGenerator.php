<?php

namespace Storm\Drivers\Platforms\Mysql\PrimaryKeys;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\PrimaryKeys;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

/**
 * Note: this is only safe when on innodb when 'innodb_autoinc_lock_mode' is equal to 0 or 1
 * as this ensures that when a multi row insert is done, the auto increments
 * are guaranteed to be sequential.
 */
class MultiAutoIncrementGenerator extends PrimaryKeys\PostMultiInsertKeyGenerator {
    protected function OnSetPrimaryKeyColumns(array $PrimaryKeyColumns) {
        if(count($PrimaryKeyColumns) !== 1) {
            throw new \Exception('Only supports single auto increment column');
        }
        else if(!reset($PrimaryKeyColumns)->HasTrait(Relational\Columns\Traits\Increment::GetType())) {
            throw new \Exception('Column must be an AUTO_INCREMENT column');
        }
    }
    
    public function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows) {
        $PrimaryKeyColumns = $this->GetPrimaryKeyColumns();
        $IncrementColumn = reset($PrimaryKeyColumns);
        //Mysql will return the first auto increment from a multi insert
        $FirstInsertId = (int)$Connection->GetLastInsertIncrement();
        $IncrementId = $FirstInsertId;
        foreach ($UnkeyedRows as $Row) {
            $Row[$IncrementColumn] = $IncrementColumn->ToPersistenceValue($IncrementId);
            $IncrementId++;
        }
    }
}

?>
