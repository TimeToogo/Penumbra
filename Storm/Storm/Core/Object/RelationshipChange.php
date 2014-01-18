<?php

namespace Storm\Core\Object;

/**
 * The class that represents a change in a relationship between entities.
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
     * @return DiscardedRelationship
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
     * @return PersistedRelationship
     */
    public function GetDiscardedRelationship() {
        return $this->DiscardedRelationship;
    }
}

?>
