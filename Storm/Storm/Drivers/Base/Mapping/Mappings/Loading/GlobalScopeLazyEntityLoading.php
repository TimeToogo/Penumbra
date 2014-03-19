<?php

namespace Storm\Drivers\Base\Mapping\Mappings\Loading;

use \Storm\Core\Mapping;
use \Storm\Drivers\Base\Mapping\Mappings\MappingException;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class GlobalScopeLazyEntityLoading extends EntityLoading {
    private $CurrentLoadIndex = 0;
    private $ParentRowArrays = [];
    private $RelatedRowArrays = [];
    
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
        $this->ParentRowArrays[] = $ParentRowArray;
        
        $RelatedRevivalDataLoader = function ($ParentRowKey) 
                use (&$EntityRelationalMap, &$Database, &$ToOneRelation, &$ParentRowArray) {  
            static $ParentRowRelatedRevivalDataMap = null;
            
            $LoadIndex = $this->CurrentLoadIndex;
            if($ParentRowRelatedRevivalDataMap === null) {
                $ParentRowRelatedRevivalDataMap = $this->LoadAllRelatedRows(
                        $EntityRelationalMap, 
                        $Database, 
                        $ToOneRelation, 
                        $ParentRowArray, 
                        $LoadIndex);
            }
            
            return $ParentRowRelatedRevivalDataMap[$ParentRowKey];
        };
        
        return $this->MapParentRowKeysToLazyRevivalData(
                $EntityRelationalMap, 
                $ToOneRelation, 
                $ParentRowArray, 
                $RelatedRevivalDataLoader);
    }
    
    private function LoadAllRelatedRows(
            Mapping\IEntityRelationalMap $EntityRelationalMap, 
            Relational\Database $Database, 
            Relational\IToOneRelation $ToOneRelation, 
            array $ParentRowArray,
            $LoadIndex) {
        
        if(count($this->ParentRowArrays) > 0 && !isset($this->RelatedRowArrays[$LoadIndex])) {
            $AllParentRows = call_user_func_array('array_merge', $this->ParentRowArrays);
            
            $this->RelatedRowArrays[$this->CurrentLoadIndex] =& $this->LoadRelatedRows(
                    $EntityRelationalMap,
                    $ToOneRelation,
                    $Database,
                    $AllParentRows);
            $this->ParentRowArrays = [];
            $this->CurrentLoadIndex++;
        }
        
        return $this->MapParentRowKeysToRelatedRevivalData(
                $EntityRelationalMap, 
                $ToOneRelation, 
                $ParentRowArray, 
                $this->RelatedRowArrays[$LoadIndex]);
    }
}

?>