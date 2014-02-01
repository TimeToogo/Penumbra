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
            throw new \InvalidArgumentException('Related identity and persistence data cannot both be null');
        }
        if($RelatedIdentity !== null && $ChildPersistenceData !== null) {
            throw new \InvalidArgumentException('Related identity and persistence data cannot both be not null');
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
     * @throws \BadMethodCallException
     */
    public function GetRelatedIdentity() {
        if($this->IsIdentifying) {
            throw new \BadMethodCallException('Relationship is identifying');
        }
        return $this->RelatedIdentity;
    }
    
    /**
     * @return Identity
     * @throws \BadMethodCallException
     */
    public function GetChildPersistenceData() {
        if(!$this->IsIdentifying) {
            throw new \BadMethodCallException('Relationship is not identifying');
        }
        return $this->ChildPersistenceData;
    }
}

?>
