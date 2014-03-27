<?php

namespace Penumbra\Drivers\Base\Object\Properties\Relationships;

use \Penumbra\Drivers\Base\Object\Properties\IRelationshipType;
use \Penumbra\Core\Object;

class Identifying implements IRelationshipType {
    
    final public function IsIdentifying() {
        return true;
    }

    public function GetPersistedEntityData(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork, 
            $ChildEntity) {
        return $UnitOfWork->PersistDependent($ChildEntity);
    }
    
    public function GetDiscardedIdentity(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork, 
            $RelatedEntity) {
        return $UnitOfWork->Discard($RelatedEntity);
    }
}


?>