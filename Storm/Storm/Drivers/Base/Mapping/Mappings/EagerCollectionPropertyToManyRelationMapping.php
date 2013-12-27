<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class CollectionPropertyToManyRelationMapping extends Mappings\CollectionPropertyToManyRelationMapping {
    public function __construct(
            Object\ICollectionProperty $CollectionProperty, 
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($CollectionProperty, $ToManyRelation);
    }
    
    public function Revive(Mapping\DomainDatabaseMap $DomainDatabaseMap, Map $ResultRowRevivalDataMap) {
        $ParentRows = $ResultRowRevivalDataMap->GetInstances();
        $RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, $ParentRows);
        $ParentRowRelatedRevivalDataArrayMap = 
                $this->MapToParentRowRelatedRevivalDataArrayMap($DomainDatabaseMap, $ParentRowRevivalDataMap, $RelatedRows);
        
        $Property = $this->GetProperty();
        foreach($ParentRowRelatedRevivalDataArrayMap as $ParentRow) {
            $RelatedRevivalDataArray = $ParentRowRelatedRevivalDataMap[$ParentRow];
            $ParentRevivalData = $ResultRowRevivalDataMap[$ParentRow];

            $ParentRevivalData[$Property] = $RelatedRevivalDataArray;
        }
    }
}

?>