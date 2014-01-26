<?php

namespace Storm\Drivers\Platforms\SQLite\PrimaryKeys;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\PrimaryKeys;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

class AutoIncrementGenerator extends PrimaryKeys\PostInsertKeyGenerator {
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
        $FirstInsertId = $Connection->FetchValue('SELECT last_insert_rowid()');
        $IncrementId = $FirstInsertId;
        foreach ($UnkeyedRows as $Row) {
            $IncrementColumn->Store($Row, $IncrementId);
            $IncrementId++;
        }
    }
}

?>
