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
        
        $RelatedRowArray = $this->LoadRelatedRows($Database, $ParentRowArray);
        
        //Groups by parent row key
        $RelatedRevivalDataArrays = $this->MapParentRowKeysToRelatedRevivalDataArray(
                $EntityRelationalMap, 
                $ToManyRelation, 
                $ParentRowArray, 
                $RelatedRowArray);
        
        return $RelatedRevivalDataArrays;
    }
}

?>