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
            throw new \Exception;//TODO:error message
        }
        
        parent::__construct($EntityProperty, $ToOneRelation);
    }
    
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array &$RevivalDataArray) {
        $PropertyIdentifier = $this->GetProperty()->GetIdentifier();
        $EntityType = $this->GetEntityType();
        
        foreach($ResultRowArray as $Key => &$RevivalData) {
            
            $RelatedEntityDataLoader = function () use (&$DomainDatabaseMap, $EntityType, $ParentRow) {
                $RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, [$ParentRow]);
                $RelatedRevivalData = $DomainDatabaseMap->MapRowsToRevivalData($EntityType, $RelatedRows);
                
                return reset($RelatedRevivalData);
            };
            
            $RevivalData[$Key][$PropertyIdentifier] = $RelatedEntityDataLoader;
        }
        
    }
}

?>