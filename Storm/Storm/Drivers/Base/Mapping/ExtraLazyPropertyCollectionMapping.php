<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class ExtraLazyPropertyCollectionMapping extends LazyPropertyCollectionMapping {
    public function __construct(
            Object\IProperty $Property, 
            $EntityType,
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($Property, $EntityType, $ToManyRelation);
    }

    public function Revive(Mapping\RevivingContext $Context, Map $ResultRowStateMap) {
        foreach($ResultRowStateMap as $ResultRow) {
            $State = $ResultRowStateMap[$ResultRow];
            $Map = new Map();
            $Map->Map($ResultRow, $State);
            parent::Revive($Context, $Map);
        }
    }
}

?>