<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class SemiLazyCollectionPropertyToManyRelationMapping extends CollectionPropertyToManyRelationMapping {
    private $ParentRowRevivalDataMaps = array();
    private $RelatedRows = array();
    
    public function __construct(
            Object\ICollectionProperty $CollectionProperty,
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($CollectionProperty, $ToManyRelation);
    }
    
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, Map $ParentRowRevivalDataMap) {
        $this->ParentRowRevivalDataMaps[] = $ParentRowRevivalDataMap;
        
        $RelatedEntityRevivalDataArrayLoader = function ($ParentRow) use (&$DomainDatabaseMap, &$ParentRowRevivalDataMap) {
            static $ParentRelatedRevivalDataArraysMap;
            
            if($ParentRelatedRevivalDataArraysMap === null) {
                $ParentRelatedRevivalDataArraysMap = $this->LoadAllRelatedRows($DomainDatabaseMap, $ParentRowRevivalDataMap);
            }
            
            return $ParentRelatedRevivalDataArraysMap[$ParentRow]->getArrayCopy();
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
    
    private function LoadAllRelatedRows(DomainDatabaseMap $DomainDatabaseMap, Map $ParentRowRevivalDataMap) {
        if(count($this->ParentRowRevivalDataMaps) > 0) {
            $ParentRows = call_user_func_array('array_merge', 
                    array_map(function (Map $Map) { return $Map->GetInstances(); }, 
                            $this->ParentRowRevivalDataMaps));

            $this->RelatedRows = array_merge(
                    $this->LoadRelatedRows($DomainDatabaseMap, $ParentRows), 
                    $this->RelatedRows);
            
            $this->ParentRowRevivalDataMaps = array();
        }
        
        return $this->MapToParentRowRelatedRevivalDataArrayMap($DomainDatabaseMap, $ParentRowRevivalDataMap, $this->RelatedRows);
    }
}

?>