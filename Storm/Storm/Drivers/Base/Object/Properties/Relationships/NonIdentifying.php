<?php

namespace Storm\Drivers\Base\Object\Properties\Relationships;

use \Storm\Drivers\Base\Object\Properties\IRelationshipType;
use \Storm\Core\Object;

class NonIdentifying extends RelationshipType {
    private $CascadePersist;
    private $CascadeDiscard;
    function __construct($CascadePersist = false, $CascadeDiscard = false) {
        $this->CascadePersist = $CascadePersist;
        $this->CascadeDiscard = $CascadeDiscard;
    }
    
    final public function WillCascadePersist() {
        return $this->CascadePersist;
    }

    final public function WillCascadeDiscard() {
        return $this->CascadeDiscard;
    }
        
    final public function IsIdentifying() {
        return false;
    }

    public function GetPersistedRelationship(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork, 
            $ParentEntity, $RelatedEntity) {
        if($this->CascadePersist && $this->IsEntityAltered($RelatedEntity)) {
            $UnitOfWork->Persist($RelatedEntity);
        }
        return $Domain->PersistedRelationship($ParentEntity, $RelatedEntity);
    }
    
    public function GetDiscardedRelationship(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork, 
            $ParentEntity, $RelatedEntity) {
        if($this->CascadeDiscard) {
            $UnitOfWork->Discard($RelatedEntity);
        }
        return $Domain->DiscardedRelationship($ParentEntity, $RelatedEntity);
    }

}


?>
