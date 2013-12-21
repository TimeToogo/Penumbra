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

    public function Revive(Mapping\RevivingContext $Context, Map $RowStateMap) {
        $Rows = iterator_to_array($RowStateMap, false);
        
        $DomainDatabaseMap = $Context->GetDomainDatabaseMap();
        $RelatedEntityType = $this->GetRelatedEntityType($Context);
        foreach($Rows as $Row) {
            $EntityState = $RowStateMap[$Row];
            $RelatedEntityLoader = function ($Instance) use (&$DomainDatabaseMap, &$Context, $Row) {
                $RelatedRow = $this->LoadRows($Context, [$Row])[0];
                $RowInstanceMap = new Map();
                $RowInstanceMap[$RelatedRow] = $Instance;
                $DomainDatabaseMap->ReviveEntityInstances($RowInstanceMap);
            };
            $EntityState[$this->GetProperty()] = 
                    $this->ProxyGenerator->GenerateProxy($RelatedEntityType, $RelatedEntityLoader);
        }
    }
}

?>