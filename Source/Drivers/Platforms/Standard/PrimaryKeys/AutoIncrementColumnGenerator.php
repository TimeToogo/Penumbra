<?php

namespace Penumbra\Drivers\Platforms\Standard\PrimaryKeys;

use \Penumbra\Drivers\Base\Relational;

trait AutoIncrementColumnGenerator {
    private $IncrementColumn;
    
    protected function OnSetPrimaryKeyColumns(array $PrimaryKeyColumns) {
        $Column = reset($PrimaryKeyColumns);
        if(count($PrimaryKeyColumns) !== 1) {
            throw new \Penumbra\Core\UnexpectedValueException(
                    '%s only supports single auto increment column: %d given',
                    get_class($this),
                    count($PrimaryKeyColumns));
        }
        else if(!$Column->HasTrait(Relational\Columns\Traits\Increment::GetType())) {
            throw new \Penumbra\Core\Relational\InvalidColumnException(
                    'The supplied column %s must contain the %s trait',
                    $Column->GetName(),
                    Relational\Columns\Traits\Increment::GetType());
        }
        $this->IncrementColumn = $Column;
    }
    
}

?>
