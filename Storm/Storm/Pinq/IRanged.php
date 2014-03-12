<?php

namespace Storm\Pinq;

interface IRanged {
    
    /**
     * Specifies the amount to skip.
     * 
     * @param int $Amount The amount of entities to skip
     * @return static
     */
    public function Skip($Amount);
    
    
    /**
     * Specifies the amount to retrieve. Pass null to remove the limit.
     * 
     * @param int|null $Amount The amount of entities to retrieve
     * @return static
     */
    public function Limit($Amount);
}

?>
