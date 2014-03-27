<?php

namespace Penumbra\Drivers\Base\Mapping\Mappings\Loading;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

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