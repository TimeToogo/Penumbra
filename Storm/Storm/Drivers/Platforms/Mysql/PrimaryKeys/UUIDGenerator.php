<?php

namespace Storm\Drivers\Platforms\Mysql\PrimaryKeys;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\PrimaryKeys;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

class UUIDGenerator extends PrimaryKeys\PreInsertKeyGenerator {
    public function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows) {
        $PrimaryKeyColumns = $this->GetPrimaryKeyColumns();
        $QueryBuilder = $Connection->QueryBuilder();
        
        $UUIDStatment = 'SELECT UPPER(REPLACE(UUID(), \'-\', \'\')) AS `UUID`';
        $UUIDStatments = array_fill(0, count($UnkeyedRows) * count($PrimaryKeyColumns), $UUIDStatment);
        $QueryBuilder->Append(implode(' UNION ', $UUIDStatments));
        
        $UUIDRows = $QueryBuilder->Build()->Execute()->FetchAll();
        $Count = 0;
        foreach($UnkeyedRows as $UnkeyedRow) {
            foreach($PrimaryKeyColumns as $PrimaryKeyColumn) {
                $UUID = $UUIDRows[$Count]['UUID'];
                $PrimaryKeyColumn->Store($UnkeyedRow, $UUID);
                $Count++;
            }
        }
    }
}

?>
