<?php

namespace Storm\Core\Relational;

/**
 * This class represents a relationship between two rows which has been discarded.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class DiscardedRelationship {
    /**
     * @var PrimaryKey 
     */
    private $ParentPrimaryKey;
    
    /**
     * @var PrimaryKey 
     */
    private $ChildPrimaryKey;
    
    public function __construct(PrimaryKey $ParentPrimaryKey, PrimaryKey $ChildPrimaryKey) {
        $this->ParentPrimaryKey = $ParentPrimaryKey;
        $this->ChildPrimaryKey = $ChildPrimaryKey;
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
