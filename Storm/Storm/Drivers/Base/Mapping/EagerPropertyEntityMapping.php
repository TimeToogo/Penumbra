<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class EagerPropertyEntityMapping extends PropertyEntityMapping {
    public function __construct( 
            Object\IProperty $Property, 
            $EntityType,
            Relational\IToOneRelation $ToOneRelation) {
        parent::__construct($Property, $EntityType, $ToOneRelation);
    }
    
    public function AddToRelationalRequest(Mapping\DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest) {
        $DomainDatabaseMap->MapEntityToRelationalRequest(
                $this->GetEntityType(),
                $RelationalRequest);
        
        $this->GetToOneRelation()->AddToRequest($RelationalRequest);
    }

    public function Revive(Mapping\RevivingContext $Context, Map $RowStateMap) {
        $Rows = iterator_to_array($RowStateMap, false);
        $RelatedEntities = $Context->ReviveEntities($this->GetEntityType(), $Rows);
        $Property = $this->GetProperty();
        foreach($Rows as $Key => $Row) {
            $EntityState = $RowStateMap[$Row];
            $EntityState[$Property] = $RelatedEntities[$Key];
        }
    }

    public function PersistRelations(Mapping\PersistingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $RelatedEntity = $Context->GetState()[$this->GetProperty()];
        return $TransactionalContext->Persist($RelatedEntity);
    }
    
    public function DiscardRelations(Mapping\DiscardingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $RelatedEntity = $Context->GetPropertyData()[$this->GetProperty()];
        return $TransactionalContext->Discard($RelatedEntity);
    }
}

?>