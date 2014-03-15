<?php

namespace Storm\Drivers\Base\Mapping\Mappings\Loading;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

interface ICollectionLoading {
    public function VerifyCompatibility(Object\ICollectionProperty $Property);
    
    public function AddLoadingRequirementsToSelect(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\IToManyRelation $ToManyRelation,
            Relational\ResultSetSelect $Select);
    
    public function Load(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\Database $Database, 
            Relational\IToManyRelation $ToManyRelation,
            array $ParentRowArray);
}

?>