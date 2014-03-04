<?php

namespace Storm\Drivers\Base\Mapping\Mappings\Loading;

use \Storm\Core\Mapping;
use \Storm\Drivers\Base\Mapping\Mappings\MappingException;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class RequestScopeEntityLoading extends EntityLoading {
    
    public function VerifyCompatibility(Object\IEntityProperty $Property) {
        if($Property->IsOptional()) {
            throw MappingException::OptionalEntityInLazyContext($Property);
        }
    }
    
    public function Load(
            Mapping\IEntityRelationalMap $EntityRelationalMap, 
            Relational\Database $Database, 
            Relational\IToOneRelation $ToOneRelation, 
            array $ParentRowArray) {
        
        $RelatedRevivalDataLoader = function ($ParentRowKey) 
                use (&$EntityRelationalMap, &$Database, &$ToOneRelation, &$ToOneRelation, &$ParentRowArray) {
            static $ParentKeyRelatedRevivalDataMap = null;
            
            if($ParentKeyRelatedRevivalDataMap === null) {
                $RelatedRows = $this->LoadRelatedRows($ToOneRelation, $Database, $ParentRowArray);
                
                $ParentKeyRelatedRevivalDataMap = $this->MapParentRowKeysToRelatedRevivalData(
                                $EntityRelationalMap, 
                                $Database,
                                $ToOneRelation, 
                                $ParentRowArray, 
                                $RelatedRows);
            }
            
            return $ParentKeyRelatedRevivalDataMap[$ParentRowKey];
        };
        
        return $this->MapParentRowKeysToLazyRevivalData(
                $EntityRelationalMap, 
                $ToOneRelation, 
                $ParentRowArray, 
                $RelatedRevivalDataLoader);
    }
}

?>