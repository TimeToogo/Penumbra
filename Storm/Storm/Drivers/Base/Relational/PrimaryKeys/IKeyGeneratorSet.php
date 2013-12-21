<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

interface IKeyGeneratorSet {
    
    /**
     * @return Column
     */
    public function Guid();
    
    /**
     * @return Column
     */
    public function Increment();
    
}

?>