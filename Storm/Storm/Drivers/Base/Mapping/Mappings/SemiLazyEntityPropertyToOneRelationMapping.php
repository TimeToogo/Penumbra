<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class SemiLazyEntityPropertyToOneRelationMapping extends EntityPropertyToOneRelationMapping {
    private $ParentRowRevivalDataMaps = array();
    private $RelatedRows = array();
    
    public function __construct(
            Object\IEntityProperty $EntityProperty, 
            Relational\IToOneRelation $ToOneRelation) {
        if($EntityProperty->IsOptional()) {
            throw new \Exception;//TODO:error message
        }
        
        parent::__construct($EntityProperty, $ToOneRelation);
        $this->ParentRowRevivalDataMapToRelatedRowsMap = new Map();
    }
    
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, Map $ParentRowRevivalDataMap) {
        $this->ParentRowRevivalDataMaps[] = $ParentRowRevivalDataMap;
        
        $RelatedEntityRevivalDataLoader = function ($ParentRow) use (&$DomainDatabaseMap, &$ParentRowRevivalDataMap) {  
            static $ParentRowRelatedRevivalDataMap = null;
            
            if($ParentRowRelatedRevivalDataMap === null) {
                $ParentRowRelatedRevivalDataMap = $this->LoadAllRelatedRows($DomainDatabaseMap, $ParentRowRevivalDataMap);
            }
            
            return $ParentRowRelatedRevivalDataMap[$ParentRow];
        };
        
        $Property = $this->GetProperty();
        foreach($ParentRowRevivalDataMap as $ParentRow) {
            $RevivalData = $ParentRowRevivalDataMap[$ParentRow];
            
            $RelatedEntityDataLoader = function () use (&$RelatedEntityRevivalDataLoader, $ParentRow) {
                return $RelatedEntityRevivalDataLoader($ParentRow);
            };
            
            $RevivalData[$Property] = $RelatedEntityDataLoader;
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
        
        return $this->MapToParentRowRelatedRevivalDataMap($DomainDatabaseMap, $ParentRowRevivalDataMap, $this->RelatedRows);
    }
}

?>