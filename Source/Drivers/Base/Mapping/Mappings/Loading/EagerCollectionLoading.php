<?php

namespace Penumbra\Drivers\Base\Mapping\Mappings\Loading;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

class EagerCollectionLoading extends CollectionLoading {
    
    public function Load(
            Mapping\IEntityRelationalMap $EntityRelationalMap, 
            Relational\Database $Database, 
            Relational\IToManyRelation $ToManyRelation, 
            array $ParentRowArray) {
        
        $RelatedRevivalDataArrays = $this->LoadRelatedRevivalDataArrayMap(
                $EntityRelationalMap, 
                $Database, 
                $ToManyRelation, 
                $ParentRowArray);
        
        return $RelatedRevivalDataArrays;
    }
}

?>