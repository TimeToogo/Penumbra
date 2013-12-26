<?php

namespace Storm\Core\Relational;

final class RelationshipChange {
    private $PersistedRelationship;
    private $DiscardedRelationship;
    
    public function __construct(Relationship $PersistedRelationship = null, Relationship $DiscardedRelationship = null) {
        $this->PersistedRelationship = $PersistedRelationship;
        $this->DiscardedRelationship = $DiscardedRelationship;
    }
    
    public function HasPersistedRelationship() {
        return $this->PersistedRelationship !== null;
    }
    
    /**
     * @return Relationship
     */
    public function GetPersistedRelationship() {
        return $this->PersistedRelationship;
    }
    
    public function HasDiscardedRelationship() {
        return $this->DiscardedRelationship !== null;
    }

    /**
     * @return Relationship
     */
    public function GetDiscardedRelationship() {
        return $this->DiscardedRelationship;
    }
}

?>
