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
    
    public function Revive(Mapping\RevivingContext $Context, Map $ResultRowStateMap) {
        $RelationalRequest = new Relational\Request(array(), true);
        $DomainDatabaseMap = $Context->GetDomainDatabaseMap();
        $DomainDatabaseMap->MapEntityToRelationalRequest($this->GetEntityType(), $RelationalRequest);
        $this->RevivalRequest($RelationalRequest, $Context, $ResultRowStateMap);
        
        $RelatedRows = $DomainDatabaseMap->GetDatabase()->Load($RelationalRequest);
        $RevivedEntities = $Context->ReviveEntities($this->GetEntityType(), $RelatedRows);
        
        $Property = $this->GetProperty();
        foreach($ResultRowStateMap as $ResultRow) {
            $MappedPrimaryKey = $this->ToOneRelation->MapToRelatedPrimaryKey($ResultRow);
            $State = $ResultRowStateMap[$ResultRow];
            $RevivedEntity = count($RevivedEntities) === 0 ? null : reset($RevivedEntities);
            $MatchedForeignKeys = array_filter($ForeignKeys, function ($ForeignKey) use (&$RelatedPrimaryKey) { 
                return $ForeignKey->Matches($RelatedPrimaryKey);
            });
            foreach($MatchedForeignKeys as $MatchedKey => $MatchedForeignKey) {
                $RelatedRowOrdered[$MatchedKey] = $RelatedRow;
                unset($ForeignKeys[$MatchedKey]);
            }
            $State[$Property] = $RevivedEntity;
        }
    }
    protected abstract function RevivalRequest(Relational\Request $RelationalRequest, Mapping\RevivingContext $Context, Map $ResultRowStateMap);
    
    
    private function OrderRelatedRows(array $ForeignKeys, array $RelatedRows) {
        $RelatedRowOrdered = array();
        
        foreach($RelatedRows as $RelatedRow) {
            $RelatedPrimaryKey = $RelatedRow->GetPrimaryKey();
            $MatchedForeignKeys = array_filter($ForeignKeys, function ($ForeignKey) use (&$RelatedPrimaryKey) { 
                return $ForeignKey->Matches($RelatedPrimaryKey);
            });
            foreach($MatchedForeignKeys as $MatchedKey => $MatchedForeignKey) {
                $RelatedRowOrdered[$MatchedKey] = $RelatedRow;
                unset($ForeignKeys[$MatchedKey]);
            }
        }
        
        return $RelatedRowOrdered;
    }
    
    final public function Persist(Mapping\PersistingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $RelatedRow = $this->PersistRelations($Context, $TransactionalContext);
        if($RelatedRow !== null)
            $this->ToOneRelation->Persist($TransactionalContext->GetTransaction(), $Context->GetRow(), $RelatedRow);
    }
    /**
     * @return Relational\Row
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