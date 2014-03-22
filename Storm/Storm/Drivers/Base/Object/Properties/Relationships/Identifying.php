<?php

namespace Storm\Drivers\Base\Object\Properties\Relationships;

use \Storm\Drivers\Base\Object\Properties\IRelationshipType;
use \Storm\Core\Object;

class Identifying implements IRelationshipType {
    
    final public function IsIdentifying() {
        return true;
    }

    public function GetPersistedEntityData(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork, 
            $ChildEntity) {
        return $Domain->Persist($UnitOfWork, $ChildEntity);
    }
    
    public function GetDiscardedIdentity(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork, 
            $RelatedEntity) {
        return $UnitOfWork->Discard($RelatedEntity);
    }
}


?>