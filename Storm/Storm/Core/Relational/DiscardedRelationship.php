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
     * @var array 
     */
    private $ParentPrimaryKey;
    
    /**
     * @var array 
     */
    private $ChildPrimaryKey;
    
    public function __construct($IsIdentifying, array &$ParentPrimaryKey, array &$ChildPrimaryKey) {
        $this->IsIdentifying = $IsIdentifying;
        $this->ParentPrimaryKey =& $ParentPrimaryKey;
        $this->ChildPrimaryKey =& $ChildPrimaryKey;
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
     */
    public function &GetRelatedPrimaryKey() {
        return $this->ChildPrimaryKey;
    }
}

?>
