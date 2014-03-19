<?php

namespace Storm\Drivers\Base\Mapping\Mappings\Loading;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

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