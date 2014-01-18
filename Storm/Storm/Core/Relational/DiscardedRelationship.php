<?php

namespace Storm\Core\Relational;

/**
 * This class represents a relationship between two rows which has been discarded.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class DiscardedRelationship {
    /**
     * @var boolean 
     */
    private $IsIdentifying;
    
    /**
     * @var PrimaryKey 
     */
    private $ParentPrimaryKey;
    
    /**
     * @var PrimaryKey 
     */
    private $ChildPrimaryKey;
    
    public function __construct($IsIdentifying, PrimaryKey $ParentPrimaryKey, PrimaryKey $ChildPrimaryKey) {
        $this->IsIdentifying = $IsIdentifying;
        $this->ParentPrimaryKey = $ParentPrimaryKey;
        $this->ChildPrimaryKey = $ChildPrimaryKey;
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
     */
    public function GetRelatedPrimaryKey() {
        return $this->ChildPrimaryKey;
    }
}

?>
