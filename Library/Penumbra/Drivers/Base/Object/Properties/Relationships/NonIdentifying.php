<?php

namespace Penumbra\Drivers\Base\Object\Properties\Relationships;

use \Penumbra\Drivers\Base\Object\Properties\IRelationshipType;
use \Penumbra\Core\Object;

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

    public function GetPersistedEntityData(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork, 
            $RelatedEntity) {
        if($this->CascadePersist) {
            return $UnitOfWork->PersistDependent($RelatedEntity);
        }
        else {
            return $Domain->Identity($RelatedEntity);
        }
    }
    
    public function GetDiscardedIdentity(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork,
            $RelatedEntity) {
        if($this->CascadeDiscard) {
            return $UnitOfWork->Discard($RelatedEntity);
        }
        else {
            return $Domain->Identity($RelatedEntity);
        }
    }
}


?>