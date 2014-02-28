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
    
    public function Revive(Relational\Database $Database, array $ResultRowArray, array $RevivalDataArray) {
        $EntityType = $this->GetEntityType();
        
        foreach($ResultRowArray as $Key => $ParentRow) {
            
            $RelatedRevivalDataLoader = function () use (&$Database, $EntityType, $ParentRow) {
                $RelatedRows = $this->LoadRelatedRows($Database, [$ParentRow]);
                $RelatedRevivalData = $this->EntityRelationalMap->MapResultRowsToRevivalData($Database, $RelatedRows);
                
                return reset($RelatedRevivalData);
            };
            
            $RevivalDataArray[$Key][$this->Property] = 
                    $this->MakeLazyRevivalData(
                            $Database, 
                            $ParentRow, 
                            $RelatedRevivalDataLoader);
        }
    }
}

?>