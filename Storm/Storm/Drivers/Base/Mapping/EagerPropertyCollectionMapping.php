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

    public function Revive(Mapping\RevivingContext $Context, Map $RowStateMap) {
        $Rows = iterator_to_array($RowStateMap, false);
        $RelatedRowsArray = $this->LoadRows($Context, $Rows);
        
        $RelatedEntityType =  $this->GetEntityType();
        foreach($Rows as $Key => $Row) {
            $RelatedRows = $RelatedRowsArray[$Key];
            $RelatedEntities = $Context->ReviveEntities($RelatedEntityType, $RelatedRows);
            $EntityState = $RowStateMap[$Row];
            $EntityState[$this->GetProperty()] = new Collections\Collection($RelatedEntities, $RelatedEntityType);
        }
    }
}

?>