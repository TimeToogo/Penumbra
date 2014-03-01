<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class SemiLazyEntityPropertyToOneRelationMapping extends EntityPropertyToOneRelationMapping {
    private $ParentRowArrays = [];
    private $RelatedRows = [];
    
    public function __construct(
            Object\IEntityProperty $EntityProperty, 
            Relational\IToOneRelation $ToOneRelation) {
        if($EntityProperty->IsOptional()) {
            throw MappingException::OptionalEntityInLazyContext($ToOneRelation);
        }
        
        parent::__construct($EntityProperty, $ToOneRelation);
    }
    
    public function __sleep() {
        return [];
    }
    
    public function __wakeup() {
        $this->ParentRowArrays = [];
        $this->RelatedRows = [];
    }
    
    public function Revive(Relational\Database $Database, array $ResultRowArray, array $RevivalDataArray) {
        $this->ParentRowArrays[] = $ResultRowArray;
        
        $RelatedRevivalDataLoader = function ($ParentRow) use (&$Database, &$ResultRowArray) {  
            static $ParentRowRelatedRevivalDataMap = null;
            
            if($ParentRowRelatedRevivalDataMap === null) {
                $ParentRowRelatedRevivalDataMap = $this->LoadAllRelatedRows($Database, $ResultRowArray);
            }
            
            return $ParentRowRelatedRevivalDataMap[$ParentRow];
        };
        
        foreach($RevivalDataArray as $Key => $RevivalData) {
            $Loader = function () use (&$RelatedRevivalDataLoader, $Key) {
                return $RelatedRevivalDataLoader($Key);
            };
            
            $RevivalData[$this->Property] = 
                    $this->MakeLazyRevivalData(
                            $ResultRowArray[$Key], 
                            $Loader);
        }
    }
    
    private function LoadAllRelatedRows(Relational\Database $Database, array $ParentRows) {
        if(count($this->ParentRowArrays) > 0) {
            $AllParentRows = call_user_func_array('array_merge', $this->ParentRowArrays);
            
            $this->RelatedRows = $this->LoadRelatedRows($Database, $AllParentRows);
            
            $this->ParentRowArrays = [];
        }
        
        return $this->MapParentRowKeysToRelatedRevivalData($Database, $ParentRows, $this->RelatedRows);
    }
}

?>