<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Object\LazyRevivalData;

class ExtraLazyCollectionPropertyToManyRelationMapping extends CollectionPropertyToManyRelationMapping {
    public function __construct(
            Object\IProperty $CollectionProperty, 
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($CollectionProperty, $ToManyRelation);
    }
    
    public function Revive(Mapping\DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array $RevivalDataArray) {
        $EntityType = $this->GetEntityType();
        
        foreach($ResultRowArray as $Key => $ParentRow) {
            
            $RelatedRevivalDataArrayLoader = 
                    function () use (&$DomainDatabaseMap, $EntityType, $ParentRow) {
                        $RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, [$ParentRow]);
                        
                        $RelatedRevivalDataArray = $DomainDatabaseMap->MapRowsToRevivalData($EntityType, $RelatedRows);

                        return $RelatedRevivalDataArray;
                    };
            
            $RevivalDataArray[$Key][$this->Property] = 
                    $this->MakeMultipleLazyRevivalData(
                            $DomainDatabaseMap, 
                            $ParentRow, 
                            $RelatedRevivalDataArrayLoader);
        }
    }
}

?>