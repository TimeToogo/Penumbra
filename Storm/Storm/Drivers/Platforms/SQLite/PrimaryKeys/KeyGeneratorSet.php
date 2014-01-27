<?php

namespace Storm\Drivers\Platforms\SQLite\PrimaryKeys;

use \Storm\Drivers\Base\Relational\PrimaryKeys;
use \Storm\Drivers\Platforms\Mysql\PrimaryKeys\IndividualAutoIncrementGenerator;

class KeyGeneratorSet implements PrimaryKeys\IKeyGeneratorSet {
    
    public function Guid() {
        throw new \Exception();
    }

    public function Increment() {
        return new IndividualAutoIncrementGenerator();
    }

    public function Sequence($Name) {
        throw new \Exception();
    }

}

?>
