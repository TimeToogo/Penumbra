<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class SemiLazyEntityPropertyToOneRelationMapping extends EntityPropertyToOneRelationMapping {
    private $ParentRowArrays = array();
    private $RelatedRows = array();
    
    public function __construct(
            Object\IEntityProperty $EntityProperty, 
            Relational\IToOneRelation $ToOneRelation) {
        if($EntityProperty->IsOptional()) {
            throw MappingException::OptionalEntityInLazyContext($ToOneRelation);
        }
        
        parent::__construct($EntityProperty, $ToOneRelation);
    }
    
    public function __sleep() {
        return array();
    }
    
    public function __wakeup() {
        $this->ParentRowArrays = array();
        $this->RelatedRows = array();
    }
    
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array $RevivalDataArray) {
        $this->ParentRowArrays[] = $ResultRowArray;
        
        $RelatedRevivalDataLoader = function ($ParentRow) use (&$DomainDatabaseMap, &$ResultRowArray) {  
            static $ParentRowRelatedRevivalDataMap = null;
            
            if($ParentRowRelatedRevivalDataMap === null) {
                $ParentRowRelatedRevivalDataMap = $this->LoadAllRelatedRows($DomainDatabaseMap, $ResultRowArray);
            }
            
            return $ParentRowRelatedRevivalDataMap[$ParentRow];
        };
        
        foreach($RevivalDataArray as $Key => $RevivalData) {
            $Loader = function () use (&$RelatedRevivalDataLoader, $Key) {
                return $RelatedRevivalDataLoader($Key);
            };
            
            $RevivalData[$this->Property] = 
                    $this->MakeLazyRevivalData(
                            $DomainDatabaseMap, 
                            $ResultRowArray[$Key], 
                            $Loader);
        }
    }
    
    private function LoadAllRelatedRows(DomainDatabaseMap $DomainDatabaseMap, array $ParentRows) {
        if(count($this->ParentRowArrays) > 0) {
            $AllParentRows = call_user_func_array('array_merge', $this->ParentRowArrays);
            
            $this->RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, $AllParentRows);
            
            $this->ParentRowArrays = array();
        }
        
        return $this->MapParentRowKeysToRelatedRevivalData($DomainDatabaseMap, $ParentRows, $this->RelatedRows);
    }
}

?>