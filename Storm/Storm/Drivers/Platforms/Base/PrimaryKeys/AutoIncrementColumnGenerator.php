<?php

namespace Storm\Drivers\Platforms\Base\PrimaryKeys;

use \Storm\Drivers\Base\Relational;

trait AutoIncrementColumnGenerator {
    private $IncrementColumn;
    
    protected function OnSetPrimaryKeyColumns(array $PrimaryKeyColumns) {
        $Column = reset($PrimaryKeyColumns);
        if(count($PrimaryKeyColumns) !== 1) {
            throw new \Storm\Core\UnexpectedValueException(
                    '%s only supports single auto increment column: %d given',
                    get_class($this),
                    count($PrimaryKeyColumns));
        }
        else if(!$Column->HasTrait(Relational\Columns\Traits\Increment::GetType())) {
            throw new \Storm\Core\Relational\InvalidColumnException(
                    'The supplied column %s must contain the %s trait',
                    $Column->GetName(),
                    Relational\Columns\Traits\Increment::GetType());
        }
        $this->IncrementColumn = $Column;
    }
    
}

?>
