<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Core\Relational\Expressions\Expression;
use \Storm\Core\Relational\IColumn;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

abstract class ReturningDataKeyGenerator implements IKeyGenerator {
    final public function GetKeyGeneratorType() {
        return KeyGeneratorType::ReturningData;
    }
    
    public abstract function AppendValueToQueryBuilder(QueryBuilder $QueryBuilder, IColumn $PrimaryKey);
    
    public function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows, array $ReturnedKeyData) {
        if(count($UnkeyedRows) !== count($ReturnedKeyData)) {
            throw new \Storm\Core\Relational\RelationalException(
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
