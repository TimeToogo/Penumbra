<?php

namespace Storm\Core\Relational;

/**
 * The class that represents a change in a relationship between rows.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class RelationshipChange {
    /**
     * @var PersistedRelationship|null 
     */
    private $PersistedRelationship;
    
    /**
     * @var DiscardedRelationship|null 
     */
    private $DiscardedRelationship;
    
    public function __construct(PersistedRelationship $PersistedRelationship = null, DiscardedRelationship $DiscardedRelationship = null) {
        $this->PersistedRelationship = $PersistedRelationship;
        $this->DiscardedRelationship = $DiscardedRelationship;
    }
    
    /**
     * @return boolean
     */
    public function HasPersistedRelationship() {
        return $this->PersistedRelationship !== null;
    }
    
    /**
     * @return PersistedRelationship|null
     */
    public function GetPersistedRelationship() {
        return $this->PersistedRelationship;
    }
    
    /**
     * @return boolean
     */
    public function HasDiscardedRelationship() {
        return $this->DiscardedRelationship !== null;
    }

    /**
     * @return DiscardedRelationship|null
     */
    public function GetDiscardedRelationship() {
        return $this->DiscardedRelationship;
    }
}

?>
