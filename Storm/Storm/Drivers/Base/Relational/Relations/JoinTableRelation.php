<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class JoinTableRelation extends ToManyRelationBase {
    /**
     * @var JoinTable 
     */
    private $JoinTable;
    /**
     * @var ForeignKey 
     */
    private $OtherForeignKey;
    
    private $RelatesToTableTwo;
    
    public function __construct(JoinTable $JoinTable, $RelatesToTableTwo = true) {
        parent::__construct(
                $RelatesToTableTwo ? $JoinTable->GetForeignKey2() : $JoinTable->GetForeignKey1(),
                $RelatesToTableTwo ? $JoinTable->GetTable2() : $JoinTable->GetTable1(), 
                Relational\DependencyOrder::After, 
                Relational\DependencyOrder::Before);
        
        $this->JoinTable = $JoinTable;
        $this->RelatesToTableTwo = $RelatesToTableTwo;
        
        $this->OtherForeignKey = !$this->RelatesToTableTwo ? 
                $this->JoinTable->GetForeignKey2() : $this->JoinTable->GetForeignKey1();
        
    }

    /**
     * @return JoinTable
     */
    final public function GetJoinTable() {
        return $this->JoinTable;
    }
        
    /**
     * @return ForeignKey
     */
    final public function GetOtherForeignKey() {
        return $this->OtherForeignKey;
    }
    
    public function AddConstraintToRequest(Relational\Request $Request) {
        $Request->AddTable($this->JoinTable);
        parent::AddConstraintToRequest($Request);
    }
    
    
    protected function ParentTable(ForeignKey $ForeignKey) { }
    protected function RelatedColumns(ForeignKey $ForeignKey) {
        return array();
    }
    public function AddParentPredicateToRequest(Relational\Request $Request, array $ParentRows) {
        if(count($ParentRows) > 1) {
            $Request->AddColumns($this->OtherForeignKey->GetParentColumns());
        }
        parent::AddParentPredicateToRequest($Request, $ParentRows);
    }
    
    protected function FillParentToRelatedRowsMap(Map $Map, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows) {
        $ParentJoinTableKeys = $this->OtherForeignKey->GetParentColumns();
        $ParentReferencedKeys = $this->OtherForeignKey->GetReferencedColumns();
        
        $GroupedRelatedRows = $this->GroupRowsByColumns($RelatedRows, $ParentJoinTableKeys);
        $this->MapParentRowsToGroupedRelatedRows($Map, $ParentRows, $ParentReferencedKeys, $GroupedRelatedRows);
    }

    protected function MapParentRowToRelatedKey(ForeignKey $ForeignKey, Relational\ResultRow $ParentRow) {
        $ParentKey = $ParentRow->GetDataFromColumns($this->OtherForeignKey->GetReferencedColumns());
        $JoinTableKey = new Relational\ResultRow($this->OtherForeignKey->GetParentColumns());
        $this->OtherForeignKey->MapReferencedToParentKey($ParentKey, $JoinTableKey);
        
        return $JoinTableKey;
    }
    
    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, array $RelationshipChanges) {
        /* @var $RelationshipChanges Relational\RelationshipChange[] */
        foreach($RelationshipChanges as $RelationshipChange) {
            if($RelationshipChange->HasPersistedRelationship()) {
                $JoinRow = $this->MapPersistedRelationshipToJoinRow($RelationshipChange->GetPersistedRelationship());
                $Transaction->Persist($JoinRow);
                if(!$JoinRow->HasPrimaryKey()) {
                    $Transaction->SubscribeToPrePersistEvent($JoinRow, function($JoinRow) use (&$ParentData) {
                        $this->OtherForeignKey->MapReferencedToParentKey(
                                $ParentData->GetRow($this->OtherForeignKey->GetReferencedTable()), 
                                $JoinRow);
                    });
                }
            }
            if($RelationshipChange->HasDiscardedRelationship()) {
                $JoinRow = $this->MapDiscardedRelationshipToJoinRow($RelationshipChange->GetDiscardedRelationship());
                $Transaction->Discard($JoinRow->GetPrimaryKey());
            }
        }
    }
    protected function PersistIdentifyingRelationship(Relational\Transaction $Transaction, Relational\Row $ParentRow, array $ChildRows) { }
    
    private function MapDiscardedRelationshipToJoinRow (Relational\DiscardedRelationship $Relationship) {
        return $this->MapRelationshipToJoinRow($Relationship);
    }
    private function MapPersistedRelationshipToJoinRow (Relational\PersistedRelationship $Relationship) {
        if($Relationship->IsIdentifying()) {
            throw new \Exception;//TODO:error message
        }
        return $this->MapRelationshipToJoinRow($Relationship);        
    }
    
    private function MapRelationshipToJoinRow($Relationship) {
        if($this->RelatesToTableTwo) {
            return $this->JoinTable->JoinRow(
                    $Relationship->GetParentPrimaryKey(), 
                    $Relationship->GetRelatedPrimaryKey());
        }
        else {
            return $this->JoinTable->JoinRow(
                    $Relationship->GetRelatedPrimaryKey(), 
                    $Relationship->GetParentPrimaryKey());               
        }
    }
}

?>