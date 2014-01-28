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
    
    public function Revive(Mapping\DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array &$RevivalDataArray) {
        $RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, $ResultRowArray);
        
        $ParentKeyRelatedRowsMap = $this->ToManyRelation->MapParentKeysToRelatedRows($ResultRowArray, $RelatedRows);
        
        $RelatedRevivalData = $DomainDatabaseMap->MapRowsToRevivalData($this->EntityType, $RelatedRows);

        $PropertyIdentifier = $this->GetProperty()->GetIdentifier();
        foreach($RevivalDataArray as $Key => &$RevivalData) {
            $RelatedRows = $ParentKeyRelatedRowsMap[$Key];
            
            $RevivalData[$PropertyIdentifier] = array_intersect_key($RelatedRevivalData, $RelatedRows);
        }
    }
}

?>