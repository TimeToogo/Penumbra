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
     * @var array 
     */
    private $ParentPrimaryKey;
    
    /**
     * @var array|null
     */
    private $RelatedPrimaryKey;
    
    /**
     * @var array|null 
     */
    private $ChildResultRow;
    
    public function __construct(array &$ParentPrimaryKey, 
            array &$RelatedPrimaryKey = null, array &$ChildResultRow = null) {
        if($RelatedPrimaryKey === null && $ChildResultRow === null) {
            throw new \InvalidArgumentException('Related primary key and result row cannot both be null');
        }
        if($RelatedPrimaryKey !== null && $ChildResultRow !== null) {
            throw new \InvalidArgumentException('Related primary key and result row cannot both be not null');
        }
        $this->IsIdentifying = $RelatedPrimaryKey === null;
        $this->ParentPrimaryKey =& $ParentPrimaryKey;
        $this->RelatedPrimaryKey =& $RelatedPrimaryKey;
        $this->ChildResultRow =& $ChildResultRow;
    }
    
    /**
     * @return boolean
     */
    public function IsIdentifying() {
        return $this->IsIdentifying;
    }
    
    /**
     * @return array
     */
    public function &GetParentPrimaryKey() {
        return $this->ParentPrimaryKey;
    }

    /**
     * @return array
     * @throws \BadMethodCallException If relationship is identifying
     */
    public function &GetRelatedPrimaryKey() {
        if($this->IsIdentifying) {
            throw new \BadMethodCallException('Relationship is identifying');
        }
        return $this->RelatedPrimaryKey;
    }

    /**
     * @return array
     * @throws \BadMethodCallException If relationship is not identifying
     */
    public function &GetChildResultRow() {
        if(!$this->IsIdentifying) {
            throw new \BadMethodCallException('Relationship is not identifying');
        }
        return $this->ChildResultRow;
    }
}

?>
