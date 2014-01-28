<?php

namespace Storm\Core\Object;

/**
 * This class represents a relationship between two entities which has been persisted.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class PersistedRelationship {
    /**
     * @var boolean
     */
    private $IsIdentifying;
    
    /**
     * @var string
     */
    private $ParentEntityType;
    
    /**
     * @var string
     */
    private $RelatedEntityType;
    
    /**
     * @var array
     */
    private $ParentIdentity;
    
    /**
     * @var array|null
     */
    private $RelatedIdentity;
    
    /**
     * @var array|null
     */
    private $ChildPersistenceData;
    
    public function __construct($ParentEntityType, $RelatedEntityType, 
            array $ParentIdentity, array $RelatedIdentity = null, array $ChildPersistenceData = null) {
        if($RelatedIdentity === null && $ChildPersistenceData === null) {
            throw new \InvalidArgumentException('Related identity and persistence data cannot both be null');
        }
        if($RelatedIdentity !== null && $ChildPersistenceData !== null) {
            throw new \InvalidArgumentException('Related identity and persistence data cannot both be not null');
        }
        $this->ParentEntityType = $ParentEntityType;
        $this->RelatedEntityType = $RelatedEntityType;
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
    
    public function GetParentEntityType() {
        return $this->ParentEntityType;
    }

    public function GetRelatedEntityType() {
        return $this->RelatedEntityType;
    }

        /**
     * @return array
     */
    public function GetParentIdentity() {
        return $this->ParentIdentity;
    }
    
    /**
     * @return array
     * @throws \BadMethodCallException
     */
    public function GetRelatedIdentity() {
        if($this->IsIdentifying) {
            throw new \BadMethodCallException('Relationship is identifying');
        }
        return $this->RelatedIdentity;
    }
    
    /**
     * @return array
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
