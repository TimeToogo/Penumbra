<?php

namespace Storm\Core\Object;

final class DiscardedRelationship {
    private $IsIdentifying;
    private $ParentIdentity;
    private $ChildIdentity;
    
    public function __construct($IsIdentifying, Identity $ParentIdentity, Identity $ChildIdentity) {
        $this->IsIdentifying = $IsIdentifying;
        $this->ParentIdentity = $ParentIdentity;
        $this->ChildIdentity = $ChildIdentity;
    }
    
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
