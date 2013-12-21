<?php

namespace Storm\Drivers\Platforms\Mysql\PrimaryKeys;

use \Storm\Drivers\Base\Relational\PrimaryKeys;

class KeyGeneratorSet implements PrimaryKeys\IKeyGeneratorSet {
    private $KeyGeneratorTable;
    function __construct(AutoIncrementGeneratorTable $KeyGeneratorTable = null) {
        $this->KeyGeneratorTable = $KeyGeneratorTable;
    }

    
    public function Guid() {
        return new UUIDGenerator();
    }

    public function Increment() {
        if($this->KeyGeneratorTable === null)
            throw new \BadMethodCallException('$KeyGeneratorTable was not supplied');
        
        return $this->KeyGeneratorTable;
    }
}

?>
