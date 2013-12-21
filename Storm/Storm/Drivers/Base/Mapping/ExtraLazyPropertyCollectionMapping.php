<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class ExtraLazyPropertyCollectionMapping extends PropertyCollectionMapping {
    public function __construct(
            Object\IProperty $Property, 
            $EntityType,
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($Property, $EntityType, $ToManyRelation);
    }

    public function Revive(Mapping\RevivingContext $Context, Map $RowStateMap) {
        $Rows = iterator_to_array($RowStateMap, false);
        $RelatedEntityType = $this->GetEntityType();
        foreach($Rows as $Key => $Row) {
            $RelatedEntitiesLoader = function () use (&$RelatedEntityType, &$Context, $Row) {
                $RelatedRows = $this->LoadRows($Context, [$Row])[0];
                return $Context->ReviveEntities($RelatedEntityType, $RelatedRows);
            };
            $EntityState = $RowStateMap[$Row];
            $EntityState[$this->GetProperty()] = new Collections\LazyCollection($RelatedEntitiesLoader, $RelatedEntityType);
        }
    }
}

?>