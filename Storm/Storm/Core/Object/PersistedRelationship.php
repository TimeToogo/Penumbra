<?php

namespace Storm\Core\Object;

/**
 * This class represents a relationship between two entities which has been persisted.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class PersistedRelationship {
    /**
     * @var boolean
     */
    private $IsIdentifying;
    
    /**
     * @var Identity
     */
    private $ParentIdentity;
    
    /**
     * @var Identity|null
     */
    private $RelatedIdentity;
    
    /**
     * @var PersistenceData|null
     */
    private $ChildPersistenceData;
    
    public function __construct(Identity $ParentIdentity, 
            Identity $RelatedIdentity = null, PersistenceData $ChildPersistenceData = null) {
        if($RelatedIdentity === null && $ChildPersistenceData === null) {
            throw new ObjectException('The supplied related identity and persistence data cannot both be null');
        }
        if($RelatedIdentity !== null && $ChildPersistenceData !== null) {
            throw new ObjectException('Either the supplied related identity or persistence data must be null');
        }
        $this->IsIdentifying = $RelatedIdentity === null;
        $this->ParentIdentity = $ParentIdentity;
        $this->RelatedIdentity = $RelatedIdentity;
        $this->ChildPersistenceData = $ChildPersistenceData;
    }
    
    /**
     * @return boolean
     */
    public function IsIdentifying() {
        return $this->IsIdentifying;
    }
       
    /**
     * @return Identity
     */
    public function GetParentIdentity() {
        return $this->ParentIdentity;
    }
    
    /**
     * @return Identity
     * @throws ObjectException
     */
    public function GetRelatedIdentity() {
        if($this->IsIdentifying) {
            throw new ObjectException('An identifying relationship does not contain a related identity');
        }
        return $this->RelatedIdentity;
    }
    
    /**
     * @return Identity
     * @throws BadMethodCallException
     */
    public function GetChildPersistenceData() {
        if(!$this->IsIdentifying) {
            throw new ObjectException('An non-identifying relationship does not contain child persistence data');
        }
        return $this->ChildPersistenceData;
    }
}

?>
