<?php

namespace Storm\Drivers\Platforms\SQLite\PrimaryKeys;

use \Storm\Drivers\Base\Relational\PrimaryKeys;

class KeyGeneratorSet implements PrimaryKeys\IKeyGeneratorSet {
    
    public function Guid() {
        throw new \Exception();
    }

    public function Increment() {
        return new AutoIncrementGenerator();
    }

    public function Sequence($Name) {
        throw new \Exception();
    }

}

?>
