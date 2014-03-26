<?php

namespace Penumbra\Drivers\Platforms\Standard\PrimaryKeys;

use \Penumbra\Core\Relational\Row;
use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;

class IndividualAutoIncrementGenerator extends PrimaryKeys\PostIndividualInsertKeyGenerator {
    use AutoIncrementColumnGenerator;
    
    public function FillPrimaryKey(IConnection $Connection, Row $UnkeyedRow) {
        $InsertId = (int)$Connection->GetLastInsertIncrement();
        $UnkeyedRow[$this->IncrementColumn] = $this->IncrementColumn->ToPersistenceValue($InsertId);
    }
}

?>
