<?php

namespace Storm\Drivers\Platforms\Base\PrimaryKeys;

use \Storm\Core\Relational\Row;
use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\PrimaryKeys;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

class IndividualAutoIncrementGenerator extends PrimaryKeys\PostIndividualInsertKeyGenerator {
    use AutoIncrementColumnGenerator;
    
    public function FillPrimaryKey(IConnection $Connection, Row $UnkeyedRow) {
        $InsertId = (int)$Connection->GetLastInsertIncrement();
        $UnkeyedRow[$this->IncrementColumn] = $this->IncrementColumn->ToPersistenceValue($InsertId);
    }
}

?>
