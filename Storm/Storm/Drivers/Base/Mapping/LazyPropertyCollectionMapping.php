<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class LazyPropertyCollectionMapping extends PropertyCollectionMapping {
    public function __construct(
            Object\IProperty $Property,
            $EntityType, 
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($Property, $EntityType, $ToManyRelation);
    }

    public function Revive(Mapping\RevivingContext $Context, Map $ResultRowStateMap) {
        $EntityType = $this->GetEntityType();
        $RelatedEntitiesArrayLoader = function ($ParentRow) use (&$EntityType, &$Context, &$ResultRowStateMap) {
            static $ParentRelatedRowArraysMap;
            if($ParentRelatedRowArraysMap === null) {
                $RelatedRows = $this->LoadRelatedRows($Context, $ResultRowStateMap);
                $ParentRelatedRowArraysMap = $this->GetRelation()
                        ->MapRelatedRows($ResultRowStateMap->GetInstances(), $RelatedRows);
            }
            
            return $Context->ReviveEntities($EntityType, $ParentRelatedRowArraysMap[$ParentRow]->getArrayCopy());
        };
        
        $Property = $this->GetProperty();
        foreach($ResultRowStateMap as $ResultRow) {
            $RelatedEntitiesLoader = function () use (&$RelatedEntitiesArrayLoader, $ResultRow) {
                return $RelatedEntitiesArrayLoader($ResultRow);
            };
            $State = $ResultRowStateMap[$ResultRow];
            $State[$Property] = new Collections\LazyCollection($RelatedEntitiesLoader, $EntityType);
        }
    }
}

?>