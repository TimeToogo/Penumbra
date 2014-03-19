<?php

namespace Storm\Drivers\Platforms\SQLite\PrimaryKeys;

use \Storm\Drivers\Base\Relational\PrimaryKeys;
use \Storm\Drivers\Platforms\Standard\PrimaryKeys\IndividualAutoIncrementGenerator;

class KeyGeneratorSet implements PrimaryKeys\IKeyGeneratorSet {
    
    private function Unsupported($KeyGeneratorType) {
        return new \Storm\Drivers\Platforms\Base\UnsupportedKeyGeneratorTypeException('SQLLite', $KeyGeneratorType);
    }
    
    
    public function Guid() {
        throw $this->Unsupported(__FUNCTION__);
    }

    public function Increment() {
        return new IndividualAutoIncrementGenerator();
    }

    public function Sequence($Name) {
        throw $this->Unsupported(__FUNCTION__);
    }

}

?>
