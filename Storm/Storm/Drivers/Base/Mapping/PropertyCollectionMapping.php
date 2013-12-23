<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

abstract class PropertyCollectionMapping extends PropertyRelationMapping implements IPropertyCollectionMapping {
    private $ToManyRelation;
    
    public function __construct( 
            Object\IProperty $Property, 
            $EntityType,
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($Property, $EntityType, $ToManyRelation);
        
        $this->ToManyRelation = $ToManyRelation;
    }
    
    /**
     * @return Relational\IToManyRelation
     */
    final protected function GetToManyRelation() {
        return $this->ToManyRelation;
    }
    
    protected function ReviveProperties(
            Map $ResultRowStateMap, 
            Map $ParentRelatedRowsMap, 
            Map $RelatedRowEntityMap) {
        $Property = $this->GetProperty();
        $EntityType = $this->GetEntityType();
        foreach($ResultRowStateMap as $ResultRow) {
            $State = $RelatedRowEntityMap[$ResultRow];
            $RelatedRows = $ParentRelatedRowsMap[$ResultRow];
            $Entities = array();
            foreach($RelatedRows as $RelatedRow) {
                $Entities[] = $RelatedRowEntityMap[$RelatedRow];
            }
            $State[$Property] = new Collections\Collection($Entities, $EntityType);
        }
    }
    
    public function Persist(Mapping\PersistingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $RelatedEntityCollection = $Context->GetState()[$this->GetProperty()];
        if(!($RelatedEntityCollection instanceof Collections\ICollection)) {
            //TODO: ERROR
            throw new Exception;
        }
        if(!$RelatedEntityCollection->__IsAltered())
            return;
                
        $PersistedRelatedRows = 
                $TransactionalContext->PersistAllRelations(iterator_to_array($RelatedEntityCollection));
        
        $RemovedEntities = $RelatedEntityCollection->__GetRemovedEntities();
        $DiscardedRelatedPrimaryKeys = $TransactionalContext->DiscardAllRelations($RemovedEntities);
        
        $this->ToManyRelation->Persist($TransactionalContext->GetTransaction(), $Context->GetColumnData(), $PersistedRelatedRows, $DiscardedRelatedPrimaryKeys);
    }
    
    public function Discard(Mapping\DiscardingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $RelatedEntityCollection = $Context->Get()[$this->GetProperty()];
        if(!($RelatedEntityCollection instanceof Collections\ICollection)) {
            //TODO: ERROR
            throw new Exception;
        }
        $OrginalEntities = $RelatedEntityCollection->__GetOriginalEntities();
        $DiscardedPrimaryKeys = $TransactionalContext->DiscardAllRelations($OrginalEntities);
        $this->ToManyRelation->Discard($TransactionalContext->GetTransaction(), $Context->GetPrimaryKey(), $DiscardedPrimaryKeys);
    }
}

?>