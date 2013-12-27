<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class LazyEntityPropertyToOneRelationMapping extends EntityPropertyToOneRelationMapping {
    public function __construct(
            Object\IEntityProperty $EntityProperty, 
            Relational\IToOneRelation $ToOneRelation) {
        if($EntityProperty->IsOptional()) {
            throw new Exception;//TODO:error message
        }
        
        parent::__construct($EntityProperty, $ToOneRelation);
    }

    public function Revive(DomainDatabaseMap $DomainDatabaseMap, Map $ParentRowRevivalDataMap) {
        $ToOneRelation = $this->GetToOneRelation();
        $RelatedEntityRevivalDataLoader = function ($ParentRow) use (&$DomainDatabaseMap, &$ToOneRelation, &$ParentRowRevivalDataMap) {
            static $ParentRowRelatedRevivalDataMap;
            if($ParentRowRelatedRevivalDataMap === null) {
                $ParentRows = $ResultRowRevivalDataMap->GetInstances();
                $RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, $ParentRows);
                
                $ParentRowRelatedRevivalDataMap = $this->MapToParentRowRelatedRevivalDataMap($DomainDatabaseMap, $ResultRowRevivalDataMap, $RelatedRows);
            }
            
            return $ParentRowRelatedRevivalDataMap[$ParentRow];
        };
        
        $Property = $this->GetProperty();
        foreach($ParentRowRevivalDataMap as $ParentRow) {
            $RevivalData = $ParentRowRevivalDataMap[$ParentRow];
            $RelatedEntityDataLoader = function () use (&$RelatedEntityRevivalDataLoader, $ParentRow) {
                $RelatedEntityRevivalDataLoader($ParentRow);
            };
            
            $RevivalData[$Property] = $RelatedEntityDataLoader;
        }
    }
}

?>