<?php

namespace Storm\Core\Object;

/**
 * The class that represents a change in a relationship between entities.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class RelationshipChange {
    /**
     * @var Identity|PersistenceData|null 
     */
    private $PersistedEntityData;
    
    /**
     * @var Identity|null 
     */
    private $DiscardedIdentity;
    
    public function __construct(EntityPropertyData $PersistedEntityData = null, Identity $DiscardedIdentity = null) {
        $this->PersistedEntityData = $PersistedEntityData;
        $this->DiscardedIdentity = $DiscardedIdentity;
    }
    
    /**
     * @return boolean
     */
    public function HasPersistedEntityData() {
        return $this->PersistedEntityData !== null;
    }
    
    /**
     * @return boolean
     */
    public function IsDependent() {
        return $this->PersistedEntityData instanceof PersistenceData;
    }
    
    /**
     * @return Identity|PersistenceData|null
     */
    public function GetPersistedEntityData() {
        return $this->PersistedEntityData;
    }
    
    /**
     * @return boolean
     */
    public function HasDiscardedIdentity() {
        return $this->DiscardedIdentity !== null;
    }

    /**
     * @return Identity|null
     */
    public function GetDiscardedIdentity() {
        return $this->DiscardedIdentity;
    }
}

?>
