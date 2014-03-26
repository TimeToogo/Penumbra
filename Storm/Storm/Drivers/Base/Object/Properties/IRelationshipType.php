<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;

interface IRelationshipType {
    public function IsIdentifying();
    
    /**
     * @return Object\EntityPropertyData
     */
    public function GetPersistedEntityData(
            Object\Domain $Domain, 
            Object\UnitOfWork $UnitOfWork, 
            $RelatedEntity);
    
    /**
     * @return Object\Identity
     */
    public function GetDiscardedIdentity(
            Object\Domain $Domain, 
            Object\UnitOfWork $UnitOfWork,
            $RelatedEntity);
}


?>