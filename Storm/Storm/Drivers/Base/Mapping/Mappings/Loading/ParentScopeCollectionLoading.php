<?php

namespace Storm\Drivers\Base\Mapping\Mappings\Loading;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class ParentScopeCollectionLoading extends CollectionLoading {
    
    public function Load(
            Mapping\IEntityRelationalMap $EntityRelationalMap, 
            Relational\Database $Database, 
            Relational\IToManyRelation $ToManyRelation, 
            array $ParentRowArray) {
        
        $LoadingValues = [];
        foreach($ParentRowArray as $Key => $ParentRow) {
            
            $RelatedRevivalDataArrayLoader = 
                    function () use (&$EntityRelationalMap, &$Database, &$ToManyRelation, $ParentRow) {
                        $RelatedRevivalDataArray = $this->LoadRelatedRevivalDataArrayMap(
                                $EntityRelationalMap,
                                $Database,
                                $ToManyRelation,
                                [$ParentRow]);

                        return $RelatedRevivalDataArray;
                    };
            
            $LoadingValues[$Key] = 
                    $this->MakeMultipleLazyRevivalData(
                            $ParentRow, 
                            $RelatedRevivalDataArrayLoader);
        }
    }
}

?>