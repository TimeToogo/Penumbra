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
    
    public function Revive(Mapping\DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array &$RevivalDataArray) {
        
        $RelatedEntityRevivalDataArrayLoader = function ($ParentRowKey) use (&$DomainDatabaseMap, &$ResultRowArray) {
            static $ParentRowKeyRevivalDataArraysMap = array();
            
            if($ParentRowKeyRevivalDataArraysMap === null) {
                
                $RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, $ResultRowArray);
                $ParentKeyRelatedRowsMap = $this->ToManyRelation->MapParentKeysToRelatedRows($ResultRowArray, $RelatedRows);
        
                $RelatedRevivalData = $DomainDatabaseMap->MapRowsToRevivalData($this->EntityType, $RelatedRows);
                
                $ParentRowKeyRevivalDataArraysMap = array();
                foreach($ResultRowArray as $Key => $ResultRowArray) {
                    $RelatedRows = $ParentKeyRelatedRowsMap[$Key];
                    
                    $ParentRowKeyRevivalDataArraysMap[$Key] = array_intersect_key($RelatedRevivalData, $RelatedRows);
                }
            }
            
            return $ParentRowKeyRevivalDataArraysMap[$ParentRowKey];
        };
        
        $PropertyIdentifier = $this->GetProperty()->GetIdentifier();
        foreach($RevivalDataArray as $Key => &$RevivalData) {
            
            $RelatedRevivalDataLoader = function () use (&$RelatedEntityRevivalDataArrayLoader, $Key) {
                return $RelatedEntityRevivalDataArrayLoader($Key);
            };
            
            $RevivalData[$PropertyIdentifier] = $RelatedRevivalDataLoader;
        }
    }
}

?>