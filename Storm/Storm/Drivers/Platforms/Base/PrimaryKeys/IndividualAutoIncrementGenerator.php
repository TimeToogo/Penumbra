<?php

namespace Storm\Drivers\Platforms\Base\PrimaryKeys;

use \Storm\Core\Relational\Row;
use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\PrimaryKeys;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

class IndividualAutoIncrementGenerator extends PrimaryKeys\PostIndividualInsertKeyGenerator {
    private $IncrementColumn;
    
    protected function OnSetPrimaryKeyColumns(array $PrimaryKeyColumns) {
        if(count($PrimaryKeyColumns) !== 1) {
            throw new \Exception('Only supports single auto increment column');
        }
        else if(!reset($PrimaryKeyColumns)->HasTrait(Relational\Columns\Traits\Increment::GetType())) {
            throw new \Exception('Column must be an AUTO_INCREMENT column');
        }
        $this->IncrementColumn = reset($PrimaryKeyColumns);
    }
    public function FillPrimaryKey(IConnection $Connection, Row $UnkeyedRow) {
        $InsertId = (int)$Connection->GetLastInsertIncrement();
        $this->IncrementColumn->Store($UnkeyedRow, $InsertId);
    }
}

?>
