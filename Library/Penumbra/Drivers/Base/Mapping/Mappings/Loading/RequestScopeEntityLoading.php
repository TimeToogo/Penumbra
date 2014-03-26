<?php

namespace Penumbra\Drivers\Base\Mapping\Mappings\Loading;

use \Penumbra\Core\Mapping;
use \Penumbra\Drivers\Base\Mapping\Mappings\MappingException;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

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
                $ParentKeyRelatedRevivalDataMap = $this->LoadRelatedRevivalDataMap(
                        $EntityRelationalMap, 
                        $Database, 
                        $ToOneRelation, 
                        $ParentRowArray);
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