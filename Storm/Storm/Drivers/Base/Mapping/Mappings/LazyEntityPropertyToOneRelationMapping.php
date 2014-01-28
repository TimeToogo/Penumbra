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
            throw new \Exception;//TODO:error message
        }
        
        parent::__construct($EntityProperty, $ToOneRelation);
    }
    
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array &$RevivalDataArray) {
        
        $RelatedEntityRevivalDataLoader = function ($ParentRowKey) use (&$DomainDatabaseMap, &$ResultRowArray) {
            static $ParentRowKeyRelatedRevivalDataMap = array();
            if($ParentRowKeyRelatedRevivalDataMap === null) {
                $RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, $ResultRowArray);
                 
                $ParentKeyRelatedRowMap = $this->ToOneRelation->MapParentKeysToRelatedRow($ResultRowArray, $RelatedRows);

                $RelatedRevivalDataArray = $DomainDatabaseMap->MapRowsToRevivalData($this->GetEntityType(), $ParentKeyRelatedRowMap);

                foreach($ResultRowArray as $Key => $ResultRow) {
                    if(isset($RelatedRevivalDataArray[$Key])) {
                        $ParentRowKeyRelatedRevivalDataMap[$Key] = $RelatedRevivalDataArray[$Key];
                    }
                    else {
                        $ParentRowKeyRelatedRevivalDataMap[$Key] = null;
                    }
                }
            }
            
            return $ParentRowKeyRelatedRevivalDataMap[$ParentRowKey];
        };
        
        $PropertyIdentifier = $this->GetProperty()->GetIdentifier();
        foreach($RevivalDataArray as $Key => &$RevivalData) {
            $RelatedRevivalDataLoader = function () use (&$RelatedEntityRevivalDataLoader, $Key) {
                return $RelatedEntityRevivalDataLoader($Key);
            };
            
            $RevivalData[$PropertyIdentifier] = $RelatedRevivalDataLoader;
        }
    }
}

?>