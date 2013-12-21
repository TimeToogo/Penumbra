<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class LazyPropertyEntityMapping extends PropertyEntityMapping {
    protected $ProxyGenerator;
    
    public function __construct( 
            Object\IProperty $Property,
            $EntityType,
            Relational\IToOneRelation $ToOneRelation,
            Proxy\IProxyGenerator $ProxyGenerator) {
        parent::__construct($Property, $EntityType, $ToOneRelation);
        
        $this->ProxyGenerator = $ProxyGenerator;
    }

    public function Revive(Mapping\RevivingContext $Context, Map $RowStateMap) {
        $Rows = iterator_to_array($RowStateMap);
        $RelatedEntitiesLoader = function ($Instance, $RelatedRowKey) use (&$Context, &$Rows) {
            static $RelatedRows;
            if($RelatedRows === null) {
                $RelatedRows = $this->LoadRows($Context, $Rows);
            }
            $RelatedRow = $RelatedRows[$RelatedRowKey];
            $RowInstanceMap = new Map();
            $RowInstanceMap[$RelatedRow] = $Instance;
            $Context->ReviveEntityInstances($RowInstanceMap);
        };
        
        $RelatedEntityType = $this->GetRelatedEntityType($Context);
        foreach($Rows as $Key => $Row) {
            $EntityState = $RowStateMap[$Row];
            $RelatedEntityLoader = function ($Instance) use (&$RelatedEntitiesLoader, $Key) {
                $RelatedEntitiesLoader($Instance, $Key);
            };
            
            $EntityState[$this->GetProperty()] = 
                    $this->ProxyGenerator->GenerateProxy($RelatedEntityType, $RelatedEntityLoader);
        }
    }

    public function PersistRelations(Mapping\PersistingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $RelatedEntity = $Context->GetState()[$this->GetProperty()];
        if($RelatedEntity instanceof Proxy\IEntityProxy) {
            if(!$RelatedEntity->__IsLoaded())
                return;
        }
        return $TransactionalContext->PersistRelations($RelatedEntity);
    }
    
    public function DiscardRelations(Mapping\DiscardingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $RelatedEntity = $Context->GetPropertyData()[$this->GetProperty()];
        return $TransactionalContext->DiscardRelations($Context->GetTransaction(), $RelatedEntity);
    }
}

?>