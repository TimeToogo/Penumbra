<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

abstract class PropertyEntityMapping extends PropertyRelationMapping implements IPropertyEntityMapping {
    private $ToOneRelation;
    
    public function __construct( 
            Object\IProperty $Property, 
            $EntityType,
            Relational\IToOneRelation $ToOneRelation) {
        parent::__construct($Property, $EntityType, $ToOneRelation);
        
        $this->ToOneRelation = $ToOneRelation;
    }
    
    /**
     * @return Relational\IToOneRelation
     */
    final protected function GetToOneRelation() {
        return $this->ToOneRelation;
    }
    
    protected function ReviveProperties(
            Map $ResultRowStateMap, 
            Map $ParentRelatedRowsMap, 
            Map $RelatedRowEntityMap) {
        $Property = $this->GetProperty();
        foreach($ResultRowStateMap as $ResultRow) {
            $State = $ResultRowStateMap[$ResultRow];
            $RelatedRow = $ParentRelatedRowsMap[$ResultRow];
            $State[$Property] = isset($RelatedRowEntityMap[$RelatedRow]) ? 
                    $RelatedRowEntityMap[$RelatedRow] : null;
        }
    }
    
    final public function Persist(Mapping\PersistingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $RelatedRow = $this->PersistRelations($Context, $TransactionalContext);
        if($RelatedRow) {
            $this->ToOneRelation->Persist($TransactionalContext->GetTransaction(), $Context->GetColumnData(), $RelatedRow->GetRow($this->ToOneRelation->GetTable()));
        }
    }
    /**
     * @return Relational\ResultRow
     */
    public abstract function PersistRelations(Mapping\PersistingContext $Context, Mapping\TransactionalContext $TransactionalContext);
    
    public function Discard(Mapping\DiscardingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $RelatedPrimaryKey = $this->DiscardRelations($Context, $TransactionalContext);
        if($RelatedPrimaryKey !== null)
            $this->ToOneRelation->Discard($TransactionalContext->GetTransaction(), $Context->GetPrimaryKey(), $RelatedPrimaryKey);
    }
    /**
     * @return Relational\PrimaryKey
     */
    public abstract function DiscardRelations(Mapping\DiscardingContext $Context, Mapping\TransactionalContext $TransactionalContext);
}

?>