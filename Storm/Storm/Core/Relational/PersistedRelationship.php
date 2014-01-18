<?php

namespace Storm\Core\Relational;

/**
 * This class represents a relationship between two rows which has been persisted.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class PersistedRelationship {
    /**
     * @var boolean 
     */
    private $IsIdentifying;
    
    /**
     * @var PrimaryKey 
     */
    private $ParentPrimaryKey;
    
    /**
     * @var PrimaryKey|null
     */
    private $RelatedPrimaryKey;
    
    /**
     * @var ResultRow|null 
     */
    private $ChildResultRow;
    
    public function __construct(PrimaryKey $ParentPrimaryKey, 
            PrimaryKey $RelatedPrimaryKey = null, ResultRow $ChildResultRow = null) {
        if($RelatedPrimaryKey === null && $ChildResultRow === null) {
            throw new \InvalidArgumentException('Related primary key and result row cannot both be null');
        }
        if($RelatedPrimaryKey !== null && $ChildResultRow !== null) {
            throw new \InvalidArgumentException('Related primary key and result row cannot both be not null');
        }
        $this->IsIdentifying = $RelatedPrimaryKey === null;
        $this->ParentPrimaryKey = $ParentPrimaryKey;
        $this->RelatedPrimaryKey = $RelatedPrimaryKey;
        $this->ChildResultRow = $ChildResultRow;
    }
    
    /**
     * @return boolean
     */
    public function IsIdentifying() {
        return $this->IsIdentifying;
    }
    
    /**
     * @return PrimaryKey
     */
    public function GetParentPrimaryKey() {
        return $this->ParentPrimaryKey;
    }

    /**
     * @return PrimaryKey
     * @throws \BadMethodCallException If relationship is identifying
     */
    public function GetRelatedPrimaryKey() {
        if($this->IsIdentifying) {
            throw new \BadMethodCallException('Relationship is identifying');
        }
        return $this->RelatedPrimaryKey;
    }

    /**
     * @return ResultRow
     * @throws \BadMethodCallException If relationship is not identifying
     */
    public function GetChildResultRow() {
        if(!$this->IsIdentifying) {
            throw new \BadMethodCallException('Relationship is not identifying');
        }
        return $this->ChildResultRow ;
    }
}

?>
