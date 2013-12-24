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
    
    public function Revive(Mapping\RevivingContext $Context, Map $ResultRowStateMap) {
        $ParentRows = iterator_to_array($ResultRowStateMap);
        $RelatedEntitiesLoader = function ($Instance, $ParentRow) use (&$Context, &$ResultRowStateMap) {
            static $ParentRelatedRowsMap;
            if($ParentRelatedRowsMap === null) {
                $RelatedRows = $this->LoadRelatedRows($Context, $ResultRowStateMap);
                $ParentRelatedRowsMap = $this->GetRelation()->MapRelatedRows($ResultRowStateMap->GetInstances(), $RelatedRows);
            }
            $RelatedRow = $ParentRelatedRowsMap[$ParentRow];
            $RowInstanceMap = new Map();
            $RowInstanceMap[$RelatedRow] = $Instance;
            $Context->ReviveEntityInstances($RowInstanceMap);
        };
        
        $RelatedEntityType = $this->GetEntityType();
        $Property = $this->GetProperty();
        foreach($ParentRows as $ParentRow) {
            $State = $ResultRowStateMap[$ParentRow];
            $RelatedEntityLoader = function ($Instance) use (&$RelatedEntitiesLoader, $ParentRow) {
                $RelatedEntitiesLoader($Instance, $ParentRow);
            };
            
            $State[$Property] = 
                    $this->ProxyGenerator->GenerateProxy($RelatedEntityType, $RelatedEntityLoader);
        }
    }

    public function PersistRelations(Mapping\PersistingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $RelatedEntity = $Context->GetState()[$this->GetProperty()];
        if($RelatedEntity instanceof Proxy\IEntityProxy) {
            if(!$RelatedEntity->__IsLoaded()) {
                return;
            }
        }
        return $TransactionalContext->Persist($RelatedEntity);
    }
    
    public function DiscardRelations(Mapping\DiscardingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $RelatedEntity = $Context->GetPropertyData()[$this->GetProperty()];
        return $TransactionalContext->Discard($Context->GetTransaction(), $RelatedEntity);
    }
}

?>