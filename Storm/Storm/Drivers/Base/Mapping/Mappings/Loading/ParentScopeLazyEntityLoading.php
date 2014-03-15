<?php

namespace Storm\Drivers\Base\Mapping\Mappings\Loading;

use \Storm\Core\Mapping;
use \Storm\Drivers\Base\Mapping\Mappings\MappingException;
use \Storm\Core\Object;
use \Storm\Core\Relational;

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
                $RelatedRows = $this->LoadRelatedRows($ToOneRelation, $Database, [$ParentRow]);
                
                return $EntityRelationalMap->MapResultRowsToRevivalData($RelatedRows)[0];
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