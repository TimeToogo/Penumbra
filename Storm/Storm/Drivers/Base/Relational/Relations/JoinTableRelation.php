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
    
    protected function GroupRelatedRowsByParentKeys(array &$MappedRelatedRows, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows) {
        $ParentJoinTableKeys = $this->OtherForeignKey->GetParentColumnIdentifiers();
        $ParentReferencedKeys = $this->OtherForeignKey->GetReferencedColumnIdentifiers();
        
        $GroupedRelatedRows = $this->GroupRowsByColumnValues($RelatedRows, $ParentJoinTableKeys);
        $KeyedParentRows = $this->MakeHashedDataToKeyMap($ParentRows, $ParentReferencedKeys);
        
        foreach($KeyedParentRows as $HashData => $ParentRowKey) {
            $MappedRelatedRows[$ParentRowKey] = isset($GroupedRelatedRows[$HashData]) ?
                    $GroupedRelatedRows[$HashData] : array();
        }
    }
    
    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, array $RelationshipChanges) {
        /* @var $RelationshipChanges Relational\RelationshipChange[] */
        $JoinRowsWithoutPrimaryKeys = array();
        foreach($RelationshipChanges as $RelationshipChange) {
            if($RelationshipChange->HasPersistedRelationship()) {
                $JoinRow =& $this->MapPersistedRelationshipToJoinRow($RelationshipChange->GetPersistedRelationship());
                $Transaction->Persist($this->JoinTable, $JoinRow);
                if(!$this->JoinTable->HasPrimaryKeyData($JoinRow)) {
                    $JoinRowsWithoutPrimaryKeys[] =& $JoinRow;
                }
            }
            if($RelationshipChange->HasDiscardedRelationship()) {
                $JoinRow = $this->MapDiscardedRelationshipToJoinRow($RelationshipChange->GetDiscardedRelationship());
                $Transaction->Discard($this->JoinTable, $this->JoinTable->GetPrimaryKeyData($JoinRow));
            }
        }
        $Transaction->SubscribeToPrePersistEvent($this->JoinTable, 
                function() use (&$ParentData, &$JoinRowsWithoutPrimaryKeys) {
                    foreach($JoinRowsWithoutPrimaryKeys as $JoinRowsWithoutPrimaryKey) {
                        $this->OtherForeignKey->MapReferencedToParentKey(
                            $ParentData, 
                            $JoinRowsWithoutPrimaryKey);
                    }
                });
    }
    protected function PersistIdentifyingRelationship(Relational\Transaction $Transaction, array $ParentRow, array &$ChildRows) { }
    
    private function &MapDiscardedRelationshipToJoinRow (Relational\DiscardedRelationship $Relationship) {
        return $this->MapRelationshipToJoinRow($Relationship);
    }
    private function &MapPersistedRelationshipToJoinRow (Relational\PersistedRelationship $Relationship) {
        if($Relationship->IsIdentifying()) {
            throw new \Exception;//TODO:error message
        }
        return $this->MapRelationshipToJoinRow($Relationship);        
    }
    
    private function &MapRelationshipToJoinRow($Relationship) {
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