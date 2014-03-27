<?php

namespace Penumbra\Drivers\Base\Mapping\Mappings\Loading;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

interface IEntityLoading {
    public function VerifyCompatibility(Object\IEntityProperty $Property);
    
    public function AddLoadingRequirementsToSelect(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\IToOneRelation $ToOneRelation,
            Relational\ResultSetSelect $Select);
    
    
    public function Load(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\Database $Database, 
            Relational\IToOneRelation $ToOneRelation,
            array $ParentRowArray);
}

?>