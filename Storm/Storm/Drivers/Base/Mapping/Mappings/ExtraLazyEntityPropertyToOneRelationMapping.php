<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Mapping\IEntityPropertyToOneRelationMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class ExtraLazyEntityPropertyToOneRelationMapping extends EntityPropertyToOneRelationMapping {
    public function __construct(
            Object\IEntityProperty $EntityProperty, 
            Relational\IToOneRelation $ToOneRelation) {
        if($EntityProperty->IsOptional()) {
            throw new Exception;//TODO:error message
        }
        
        parent::__construct($EntityProperty, $ToOneRelation);
    }

    public function Revive(DomainDatabaseMap $DomainDatabaseMap, Map $ResultRowRevivalDataMap) {        
        $Property = $this->GetProperty();
        $EntityType = $this->GetEntityType();
        $ToOneRelation = $this->GetToOneRelation();
        $Database = $DomainDatabaseMap->GetDatabase();
        foreach($ResultRowRevivalDataMap as $ParentRow) {
            $RevivalData = $ResultRowRevivalDataMap[$ParentRow];
            $RelatedEntityDataLoader = function () use (&$DomainDatabaseMap, &$Database, &$ToOneRelation, $EntityType, $ParentRow) {
                $RelatedRowRequest = $ToOneRelation->RelationRequest([$ParentRow]);
                $RelatedRows = $Database->Load($RelatedRowRequest);
                $RelatedRevivalData = $DomainDatabaseMap->MapRowsToRevivalData($EntityType, $RelatedRows);
                
                return reset($RelatedRevivalData);
            };
            
            $RevivalData[$Property] = $RelatedEntityDataLoader;
        }
    }

}

?>