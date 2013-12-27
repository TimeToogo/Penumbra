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
    public function Revive(Mapping\DomainDatabaseMap $DomainDatabaseMap, Map $ParentRowRevivalDataMap) {
        $ToManyRelation = $this->GetToManyRelation();
        $RelatedEntityRevivalDataArrayLoader = function ($ParentRow) use (&$DomainDatabaseMap, &$ToManyRelation, &$ParentRowRevivalDataMap) {
            static $ParentRelatedRevivalDataArraysMap;
            if($ParentRelatedRevivalDataArraysMap === null) {
                $ParentRows = $ResultRowRevivalDataMap->GetInstances();
                $RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, $ParentRows);
                
                $ParentRelatedRevivalDataArraysMap = $this->MapToParentRowRelatedRevivalDataArrayMap($DomainDatabaseMap, $ResultRowRevivalDataMap, $RelatedRows);
            }
            
            return $ParentRelatedRevivalDataArraysMap[$ParentRow];
        };
        
        $Property = $this->GetProperty();
        foreach($ParentRowRevivalDataMap as $ParentRow) {
            $RevivalData = $ParentRowRevivalDataMap[$ParentRow];            
            $RelatedEntityRevivalDataLoader = function () use (&$RelatedEntityRevivalDataArrayLoader, $ParentRow) {
                return $RelatedEntityRevivalDataArrayLoader($ParentRow);
            };
            
            $RevivalData[$Property] = $RelatedEntityRevivalDataLoader;
        }
    }
}

?>