<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class ExtraLazyPropertyEntityMapping extends LazyPropertyEntityMapping {
    
    public function __construct(
            Object\IProperty $Property, 
            $EntityType,
            Relational\IToOneRelation $ToOneRelation, 
            Proxy\IProxyGenerator $ProxyGenerator) {
        parent::__construct($Property, $EntityType, $ToOneRelation, $ProxyGenerator);
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