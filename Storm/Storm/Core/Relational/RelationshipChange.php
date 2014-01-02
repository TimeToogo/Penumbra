<?php

namespace Storm\Core\Relational;

final class RelationshipChange {
    private $PersistedRelationship;
    private $DiscardedRelationship;
    
    public function __construct(PersistedRelationship $PersistedRelationship = null, DiscardedRelationship $DiscardedRelationship = null) {
        $this->PersistedRelationship = $PersistedRelationship;
        $this->DiscardedRelationship = $DiscardedRelationship;
    }
    
    public function HasPersistedRelationship() {
        return $this->PersistedRelationship !== null;
    }
    
    /**
     * @return PersistedRelationship
     */
    public function GetPersistedRelationship() {
        return $this->PersistedRelationship;
    }
    
    public function HasDiscardedRelationship() {
        return $this->DiscardedRelationship !== null;
    }

    /**
     * @return DiscardedRelationship
     */
    public function GetDiscardedRelationship() {
        return $this->DiscardedRelationship;
    }
}

?>
