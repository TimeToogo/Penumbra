<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class SemiLazyCollectionPropertyToManyRelationMapping extends CollectionPropertyToManyRelationMapping {
    private $ParentRowArrays = [];
    private $RelatedRows = [];
    
    public function __construct(
            Object\ICollectionProperty $CollectionProperty,
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($CollectionProperty, $ToManyRelation);
    }
    
    public function __sleep() {
        return [];
    }
    
    public function __wakeup() {
        $this->ParentRowArrays = [];
        $this->RelatedRows = [];
    }
    
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array $RevivalDataArray) {
        $this->ParentRowArrays[] = $ResultRowArray;
        
        $RelatedRevivalDataArrayLoader = function ($ParentRowKey) use (&$DomainDatabaseMap, &$ResultRowArray) {
            static $ParentKeyRelatedRevivalDataArraysMap = null;
            
            if($ParentKeyRelatedRevivalDataArraysMap === null) {
                $ParentKeyRelatedRevivalDataArraysMap = $this->LoadAllRelatedRows($DomainDatabaseMap, $ResultRowArray);
            }
            
            return $ParentKeyRelatedRevivalDataArraysMap[$ParentRowKey];
        };
        
        foreach($RevivalDataArray as $Key => $RevivalData) {
            $Loader = function () use (&$RelatedRevivalDataArrayLoader, $Key) {
                return $RelatedRevivalDataArrayLoader($Key);
            };
            
            $RevivalData[$this->Property] = 
                    $this->MakeMultipleLazyRevivalData(
                            $DomainDatabaseMap, 
                            $ResultRowArray[$Key], 
                            $Loader);
        }
    }
    
    private function LoadAllRelatedRows(DomainDatabaseMap $DomainDatabaseMap, array $ParentRows) {
        if(count($this->ParentRowArrays) > 0) {
            $AllParentRows = call_user_func_array('array_merge', $this->ParentRowArrays);
            
            $this->RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, $AllParentRows);
            
            $this->ParentRowArrays = [];
        }
        
        return $this->MapParentRowKeysToRelatedRevivalDataArray($DomainDatabaseMap, $ParentRows, $this->RelatedRows);
    }
}

?>