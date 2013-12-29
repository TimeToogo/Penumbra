<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Core\Relational\Expressions\Expression;

abstract class ExpressionWithReturningDataKeyGenerator implements IKeyGenerator {
    final public function GetKeyGeneratorMode() {
        return KeyGeneratorMode::ExpressionWithReturningData;
    }
    
    /**
     * @return Expression
     */
    public abstract function GetExpression(array $PrimaryKeyColumns);
    public function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows, array $ReturnedKeyData) {
        if(count($UnkeyedRows) !== count($ReturnedKeyData)) {
            throw new Exception;//TODO:error message
        }
        else {
            $this->FillPrimaryKeyValues($Connection, array_values($UnkeyedRows), array_values($ReturnedKeyData));
        }
    }
    protected abstract function FillPrimaryKeyValues(IConnection $Connection, array $UnkeyedRowsValues, array $ReturnedKeyDataValues);
}

?>
