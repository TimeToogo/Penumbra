<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class LazyCollectionPropertyToManyRelationMapping extends CollectionPropertyToManyRelationMapping {
    public function __construct(
            Object\ICollectionProperty $CollectionProperty,
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($CollectionProperty, $ToManyRelation);
    }
    
    public function Revive(Mapping\DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array $RevivalDataArray) {
        $RelatedRevivalDataArrayLoader = function ($ParentRowKey) use (&$DomainDatabaseMap, &$ResultRowArray) {
            static $ParentKeyRevivalDataArraysMap = null;
            
            if($ParentKeyRevivalDataArraysMap === null) {
                $RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, $ResultRowArray);
                $ParentKeyRevivalDataArraysMap = $this->MapParentRowKeysToRelatedRevivalDataArray($DomainDatabaseMap, $ResultRowArray, $RelatedRows);
            }
            
            return $ParentKeyRevivalDataArraysMap[$ParentRowKey];
        };
        
        foreach($RevivalDataArray as $Key => $RevivalData) {
            $Loader = function () use (&$RelatedRevivalDataArrayLoader, $Key) {
                return $RelatedRevivalDataArrayLoader($Key);
            };
            
            $RevivalData[$this->Property] = 
                    $this->MakeMultipleLazyRevivalData(
                            $DomainDatabaseMap, 
                            $ResultRowArray[$Key], 
                            $Loader);
        }
    }
}

?>