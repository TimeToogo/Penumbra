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
            throw new RelationalException('The supplied related primary key and child result row cannot both be null');
        }
        if($RelatedPrimaryKey !== null && $ChildResultRow !== null) {
            throw new RelationalException('Either the supplied related primary key or child result row must be null');
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
     * @throws RelationalException If relationship is identifying
     */
    public function GetRelatedPrimaryKey() {
        if($this->IsIdentifying) {
            throw new RelationalException('An identifying relationship does not contain a related primary key');
        }
        return $this->RelatedPrimaryKey;
    }

    /**
     * @return ResultRow
     * @throws RelationalException If relationship is not identifying
     */
    public function GetChildResultRow() {
        if(!$this->IsIdentifying) {
            throw new RelationalException('A non-identifying relationship does not contain a child result row');
        }
        return $this->ChildResultRow ;
    }
}

?>
