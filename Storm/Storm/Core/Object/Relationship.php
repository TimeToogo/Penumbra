<?php

namespace Storm\Core\Object;

final class Relationship {
    private $ParentIdentity;
    private $ChildIdentity;
    
    public function __construct(Identity $ParentIdentity, Identity $ChildIdentity) {
        $this->ParentIdentity = $ParentIdentity;
        $this->ChildIdentity = $ChildIdentity;
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
    public function GetChildIdentity() {
        return $this->ChildIdentity;
    }
}

?>
