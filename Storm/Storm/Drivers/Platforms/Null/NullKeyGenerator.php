<?php

namespace Storm\Drivers\Platforms\Null;

use \Storm\Drivers\Base\Relational;

final class NullKeyGenerator implements Relational\PrimaryKeys\IKeyGenerator {
    public function FillPrimaryKeys(Relational\Queries\IConnection $Connection, 
            Relational\Table $Table, array $PrimaryKeys, array $PrimaryKeyColumns) { }

    public function GetKeyGeneratorMode() {
        
    }

    public function SetTable(\Storm\Core\Relational\Table $Table) {
        
    }

}

?>