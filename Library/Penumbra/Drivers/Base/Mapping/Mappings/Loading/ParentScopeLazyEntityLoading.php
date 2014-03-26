<?php

namespace Penumbra\Drivers\Base\Mapping\Mappings\Loading;

use \Penumbra\Core\Mapping;
use \Penumbra\Drivers\Base\Mapping\Mappings\MappingException;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

class ParentScopeLazyEntityLoading extends EntityLoading {
    
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
        
        $LoadingValues = array();
        foreach($ParentRowArray as $Key => $ParentRow) {
            $RelatedRevivalDataLoader = function () use (&$EntityRelationalMap, &$Database, &$ToOneRelation, $ParentRow) {
                return $this->LoadRelatedRevivalDataMap(
                        $EntityRelationalMap,
                        $Database,
                        $ToOneRelation,
                        [$ParentRow])[0];
            };
            
            $LoadingValues[$Key] = $this->MakeLazyRevivalData(
                    $EntityRelationalMap, 
                    $ToOneRelation, 
                    $ParentRow, 
                    $RelatedRevivalDataLoader);
        }
        
        return $LoadingValues;
    }
}

?>