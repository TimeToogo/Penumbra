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
     * @return string
     */
    private $ParentEntityType;
    
    /**
     * @return string
     */
    private $RelatedEntityType;
    
    
    /**
     * @return array
     */
    private $ParentIdentity;
    
    /**
     * @return array
     */
    private $ChildIdentity;
    
    public function __construct($IsIdentifying, $ParentEntityType, $RelatedEntityType, array $ParentIdentity, array $ChildIdentity) {
        $this->ParentEntityType = $ParentEntityType;
        $this->RelatedEntityType = $RelatedEntityType;
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
     */
    public function GetRelatedIdentity() {
        return $this->ChildIdentity;
    }
}

?>
