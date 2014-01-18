<?php

namespace Storm\Core\Object;

/**
 * This class represents a relationship between two entities which has been discarded.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class DiscardedRelationship {
    /**
     * @var boolean 
     */
    private $IsIdentifying;
    
    /**
     * @return Identity
     */
    private $ParentIdentity;
    
    /**
     * @return Identity
     */
    private $ChildIdentity;
    
    public function __construct($IsIdentifying, Identity $ParentIdentity, Identity $ChildIdentity) {
        $this->IsIdentifying = $IsIdentifying;
        $this->ParentIdentity = $ParentIdentity;
        $this->ChildIdentity = $ChildIdentity;
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
     */
    public function GetRelatedIdentity() {
        return $this->ChildIdentity;
    }
}

?>
