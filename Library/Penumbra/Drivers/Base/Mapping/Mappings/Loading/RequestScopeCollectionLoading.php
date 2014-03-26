<?php

namespace Penumbra\Drivers\Base\Mapping\Mappings\Loading;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

class RequestScopeCollectionLoading extends CollectionLoading {
    
    public function Load(
            Mapping\IEntityRelationalMap $EntityRelationalMap, 
            Relational\Database $Database, 
            Relational\IToManyRelation $ToManyRelation, 
            array $ParentRowArray) {
        
        $RelatedRevivalDataArrayLoader = function ($ParentRowKey) 
                use (&$EntityRelationalMap, &$Database, &$ToManyRelation, &$ParentRowArray) {  
            static $ParentRowRelatedRevivalDataArrayMap = null;
            
            if($ParentRowRelatedRevivalDataArrayMap === null) {
                $ParentRowRelatedRevivalDataArrayMap = $this->LoadRelatedRevivalDataArrayMap(
                        $EntityRelationalMap, 
                        $Database, 
                        $ToManyRelation, 
                        $ParentRowArray);
            }
            
            return $ParentRowRelatedRevivalDataArrayMap[$ParentRowKey];
        };
        
        return $this->MapParentRowKeysToMultipleLazyRevivalData(
                $EntityRelationalMap, 
                $ToManyRelation, 
                $ParentRowArray, 
                $RelatedRevivalDataArrayLoader);
    }
}

?>