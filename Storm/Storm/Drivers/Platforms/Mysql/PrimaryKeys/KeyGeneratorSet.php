<?php

namespace Storm\Drivers\Platforms\Mysql\PrimaryKeys;

use \Storm\Drivers\Base\Relational\PrimaryKeys;

class KeyGeneratorSet implements PrimaryKeys\IKeyGeneratorSet {
    private $SequenceTable;
    
    public function SetSequenceTable(SequenceTable $SequenceTable) {
        $this->SequenceTable = $SequenceTable;
    }
    
    public function Guid() {
        return new UUIDGenerator();
    }

    public function Increment() {
        return new AutoIncrementGenerator();
    }

    public function Sequence($Name) {
        if($this->SequenceTable === null) {
            throw new \BadMethodCallException('$KeyGeneratorTable was not supplied');
        }
        
        return new SequenceGenerator($Name, $this->SequenceTable);
    }

}

?>
