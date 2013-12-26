<?php

namespace Storm\Core\Relational;

final class Relationship {
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
    public function GetChildPrimaryKey() {
        return $this->ChildPrimaryKey;
    }
}

?>
