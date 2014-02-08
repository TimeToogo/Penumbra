<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Mapping;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class ExtraLazyEntityPropertyToOneRelationMapping extends EntityPropertyToOneRelationMapping {
    public function __construct(
            Object\IEntityProperty $EntityProperty, 
            Relational\IToOneRelation $ToOneRelation) {
        if($EntityProperty->IsOptional()) {
            throw MappingException::OptionalEntityInLazyContext($ToOneRelation);
        }
        
        parent::__construct($EntityProperty, $ToOneRelation);
    }
    
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array $RevivalDataArray) {
        $EntityType = $this->GetEntityType();
        
        foreach($ResultRowArray as $Key => $ParentRow) {
            
            $RelatedRevivalDataLoader = function () use (&$DomainDatabaseMap, $EntityType, $ParentRow) {
                $RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, [$ParentRow]);
                $RelatedRevivalData = $DomainDatabaseMap->MapRowsToRevivalData($EntityType, $RelatedRows);
                
                return reset($RelatedRevivalData);
            };
            
            $RevivalDataArray[$Key][$this->Property] = 
                    $this->MakeLazyRevivalData(
                            $DomainDatabaseMap, 
                            $ParentRow, 
                            $RelatedRevivalDataLoader);
        }
    }
}

?>