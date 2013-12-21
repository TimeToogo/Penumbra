<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class LazyPropertyCollectionMapping extends PropertyCollectionMapping {
    public function __construct(
            Object\IProperty $Property,
            $EntityType, 
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($Property, $EntityType, $ToManyRelation);
    }

    public function Revive(Mapping\RevivingContext $Context, Map $RowStateMap) {
        $Rows = iterator_to_array($RowStateMap, false);
        $RelatedEntityType = $this->GetRelatedEntityType($Context);
        $RelatedEntitiesArrayLoader = function ($Key) use (&$RelatedEntityType, &$Context, &$Rows) {
            static $RelatedRowsArray;
            if($RelatedRowsArray === null) {
                $RelatedRowsArray = $this->LoadRows($Context, $Rows);
            }
            
            return $Context->ReviveEntities($RelatedEntityType, $RelatedRowsArray[$Key]);
        };
        foreach($Rows as $Key => $Row) {
            $RelatedEntitiesLoader = function () use (&$RelatedEntitiesArrayLoader, $Key) {
                return $RelatedEntitiesArrayLoader($Key);
            };
            $EntityState = $RowStateMap[$Row];
            $EntityState[$this->GetProperty()] = new Collections\LazyCollection($RelatedEntitiesLoader, $RelatedEntityType);
        }
    }
}

?>