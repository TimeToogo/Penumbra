<?php

namespace Storm\Drivers\Base\Object\Properties\Relationships;

use \Storm\Drivers\Base\Object\Properties\IRelationshipType;
use \Storm\Core\Object;

class Identifying implements IRelationshipType {
    
    final public function IsIdentifying() {
        return true;
    }

    public function GetPersistedRelationship(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork, 
            $ParentEntity, $ChildEntity) {
        return $Domain->PersistedIdentifyingRelationship($ParentEntity, $ChildEntity, $UnitOfWork);
    }
    
    public function GetDiscardedRelationship(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork, 
            $ParentEntity, $ChildEntity) {
        return $Domain->DiscardedIdentifyingRelationship($ParentEntity, $ChildEntity, $UnitOfWork);
    }
}


?>
