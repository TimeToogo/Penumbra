<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

interface IKeyGeneratorSet {
    
    /**
     * @return IKeyGenerator
     */
    public function Guid();
    
    /**
     * @return IKeyGenerator
     */
    public function Increment();
    
    /**
     * @return IKeyGenerator
     */
    public function Sequence();
    
}

?>