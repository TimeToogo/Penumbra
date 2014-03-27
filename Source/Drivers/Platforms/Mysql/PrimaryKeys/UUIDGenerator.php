<?php

namespace Penumbra\Drivers\Platforms\Mysql\PrimaryKeys;

use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;

class UUIDGenerator extends PrimaryKeys\PreInsertKeyGenerator {
        
    public function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows) {
        if(count($UnkeyedRows) === 0) {
            return;
        }
        $PrimaryKeyColumns = $this->GetPrimaryKeyColumns();
        $QueryBuilder = $Connection->QueryBuilder();
        
        $UUIDStatment = 'SELECT UUID()';
        $UUIDStatments = array_fill(0, count($UnkeyedRows) * count($PrimaryKeyColumns) - 1, $UUIDStatment);
        array_unshift($UUIDStatments, $UUIDStatment . ' AS `UUID`');
        $QueryBuilder->Append(implode(' UNION ALL ', $UUIDStatments));
        
        $UUIDRows = $QueryBuilder->Build()->Execute()->FetchAll();
        $Count = 0;
        foreach($UnkeyedRows as $UnkeyedRow) {
            foreach($PrimaryKeyColumns as $PrimaryKeyColumn) {
                $UUID = $UUIDRows[$Count]['UUID'];
                $UnkeyedRow[$PrimaryKeyColumn] = $PrimaryKeyColumn->ToPersistenceValue($UUID);
                $Count++;
            }
        }
    }
}

?>
