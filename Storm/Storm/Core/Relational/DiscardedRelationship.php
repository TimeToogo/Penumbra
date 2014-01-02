<?php

namespace Storm\Core\Relational;

final class DiscardedRelationship {
    private $ParentPrimaryKey;
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
