<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class EagerCollectionPropertyToManyRelationMapping extends CollectionPropertyToManyRelationMapping {
    public function __construct(
            Object\ICollectionProperty $CollectionProperty, 
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($CollectionProperty, $ToManyRelation);
    }
    
    public function Revive(Mapping\DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array $RevivalDataArray) {
        $RelatedRowArray = $this->LoadRelatedRows($DomainDatabaseMap, $ResultRowArray);
        
        $ParentKeyRelatedRevivalDataArrayMap = $this->MapParentRowKeysToRelatedRevivalDataArray($DomainDatabaseMap, $ResultRowArray, $RelatedRowArray);
        
        foreach($RevivalDataArray as $Key => $RevivalData) {            
            $RevivalData[$this->Property] = $ParentKeyRelatedRevivalDataArrayMap[$Key];
        }
    }
}

?>