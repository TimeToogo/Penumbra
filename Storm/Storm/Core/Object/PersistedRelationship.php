<?php

namespace Storm\Core\Object;

final class PersistedRelationship {
    private $IsIdentifying;
    private $ParentIdentity;
    private $RelatedIdentity;
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
     */
    public function GetRelatedIdentity() {
        if($this->IsIdentifying) {
            throw new \BadMethodCallException('Relationship is identifying');
        }
        return $this->RelatedIdentity;
    }

    /**
     * @return PersistenceData
     */
    public function GetChildPersistenceData() {
        if(!$this->IsIdentifying) {
            throw new \BadMethodCallException('Relationship is not identifying');
        }
        return $this->ChildPersistenceData;
    }
}

?>
