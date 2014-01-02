<?php

namespace Storm\Drivers\Platforms\Mysql\PrimaryKeys;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\PrimaryKeys;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

//Note: this is only safe when 'innodb_autoinc_lock_mode' is equal to 0 or 1
class AutoIncrementGenerator extends PrimaryKeys\PostInsertKeyGenerator {
    protected function OnSetPrimaryKeyColumns(array $PrimaryKeyColumns) {
        if(count($PrimaryKeyColumns) !== 1) {
            throw new \Exception('Only supports single auto increment column');
        }
        else if(!$PrimaryKeyColumns[0]->HasTrait(Relational\Columns\Traits\Increment::GetType())) {
            throw new \Exception('Column must be an AUTO_INCREMENT column');
        }
    }
    
    public function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows) {
        $PrimaryKeyColumn = reset($this->GetPrimaryKeyColumns());
        $FirstInsertId = $Connection->FetchValue('SELECT LAST_INSERT_ID()');
        $IncrementId = $FirstInsertId;
        foreach ($UnkeyedRows as $Row) {
            $IncrementColumn->Store($Row, $IncrementId);
            $IncrementId++;
        }
    }
}

?>
