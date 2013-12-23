<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class EagerPropertyCollectionMapping extends PropertyCollectionMapping {
    public function __construct(
            Object\IProperty $Property, 
            $EntityType,
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($Property, $EntityType, $ToManyRelation);
    }

    public function Revive(Mapping\RevivingContext $Context, Map $ParentRowStateMap) {
        $RelatedRows = $this->LoadRelatedRows($Context, $ParentRowStateMap);
        $ParentRelatedRowArraysMap = $this->GetRelation()
                ->MapRelatedRows($ParentRowStateMap->GetInstances(), $RelatedRows);
        
        $RelatedEntityType =  $this->GetEntityType();
        $Property = $this->GetProperty();
        foreach($ParentRelatedRowArraysMap as $ParentRow) {
            $RelatedRows = $ParentRelatedRowArraysMap[$ParentRow];
            $RelatedEntities = $Context->ReviveEntities($RelatedEntityType, $RelatedRows);
            
            $State = $ParentRowStateMap[$ParentRow];
            $State[$Property] = new Collections\Collection($RelatedEntities, $RelatedEntityType);
        }
    }
}

?>