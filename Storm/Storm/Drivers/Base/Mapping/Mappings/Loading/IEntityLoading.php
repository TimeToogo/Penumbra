<?php

namespace Storm\Drivers\Base\Mapping\Mappings\Loading;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

interface IEntityLoading {
    public function VerifyCompatibility(Object\IEntityProperty $Property);
    
    public function AddToRelationalRequest(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\IToOneRelation $ToOneRelation,
            Relational\Select $RelationalRequest);
    
    
    public function Load(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\Database $Database, 
            Relational\IToOneRelation $ToOneRelation,
            array $ParentRowArray);
}

?>