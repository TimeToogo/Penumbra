<?php

namespace Penumbra\Drivers\Base\Relational\PrimaryKeys;

use \Penumbra\Core\Relational\Expression;
use \Penumbra\Core\Relational\IColumn;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;

abstract class ReturningDataKeyGenerator implements IKeyGenerator {
    final public function GetKeyGeneratorType() {
        return KeyGeneratorType::ReturningData;
    }
    
    public abstract function AppendValueToQueryBuilder(QueryBuilder $QueryBuilder, IColumn $PrimaryKey);
    
    public function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows, array $ReturnedKeyData) {
        if(count($UnkeyedRows) !== count($ReturnedKeyData)) {
            throw new \Penumbra\Core\Relational\RelationalException(
                    'The amouny unkeyed rows must match the amount of returned key data arrays: %d rows != %d key data',
                    count($UnkeyedRows),
                    count($ReturnedKeyData));
        }
        else {
            $this->FillPrimaryKeyValues($Connection, array_values($UnkeyedRows), array_values($ReturnedKeyData));
        }
    }
    protected abstract function FillPrimaryKeyValues(IConnection $Connection, array $UnkeyedRowsValues, array $ReturnedKeyDataValues);
}

?>
