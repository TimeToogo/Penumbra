<?php

namespace Storm\Core\Relational;

final class PersistedRelationship {
    private $IsIdentifying;
    private $ParentPrimaryKey;
    private $RelatedPrimaryKey ;
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
     */
    public function GetRelatedPrimaryKey() {
        if($this->IsIdentifying) {
            throw new \BadMethodCallException('Relationship is identifying');
        }
        return $this->RelatedPrimaryKey;
    }

    /**
     * @return ResultRow
     */
    public function GetChildResultRow() {
        if(!$this->IsIdentifying) {
            throw new \BadMethodCallException('Relationship is not identifying');
        }
        return $this->ChildResultRow ;
    }
}

?>
