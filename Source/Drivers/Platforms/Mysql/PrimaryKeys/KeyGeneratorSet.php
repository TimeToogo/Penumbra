<?php

namespace Penumbra\Drivers\Platforms\Mysql\PrimaryKeys;

use \Penumbra\Drivers\Base\Relational\PrimaryKeys;

class KeyGeneratorSet implements PrimaryKeys\IKeyGeneratorSet {
    private $SequenceTable;
    
    public function SetSequenceTable(SequenceTable $SequenceTable) {
        $this->SequenceTable = $SequenceTable;
    }
    
    public function Guid() {
        return new UUIDGenerator();
    }

    public function Increment() {
        return new MultiAutoIncrementGenerator();
    }

    public function Sequence($Name) {
        if($this->SequenceTable === null) {
            throw new \Penumbra\Core\NotSupportedException(
                    'Must supply sequence table for a sequence generator');
        }
        
        return new SequenceGenerator($Name, $this->SequenceTable);
    }

}

?>
