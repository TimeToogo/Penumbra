<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;

interface IRelationshipType {
    public function IsIdentifying();
    public function GetPersistedRelationship(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork,
            $ParentEntity, $RelatedEntity);
    public function GetDiscardedRelationship(Object\Domain $Domain, Object\UnitOfWork $UnitOfWork,
            $ParentEntity, $RelatedEntity);
}


?>
