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
    private $ParentForeignKey;
    
    private $RelatesToTableTwo;
    
    public function __construct(JoinTable $JoinTable, $RelatesToTableTwo = true) {
        parent::__construct(
                $RelatesToTableTwo ? $JoinTable->GetForeignKey2() : $JoinTable->GetForeignKey1(),
                $RelatesToTableTwo ? $JoinTable->GetTable2() : $JoinTable->GetTable1(), 
                Relational\DependencyOrder::After, 
                Relational\DependencyOrder::Before);
        
        $this->JoinTable = $JoinTable;
        $this->RelatesToTableTwo = $RelatesToTableTwo;
        
        $this->ParentForeignKey = !$this->RelatesToTableTwo ? 
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
        return $this->ParentForeignKey;
    }
    
    public function GetParentTable() {
        return $this->RelatesToTableTwo ?
                $this->JoinTable->GetTable1() : $this->JoinTable->GetTable2();
    }
    
    public function GetParentColumns() {
        return $this->ParentForeignKey->GetReferencedColumns();
    }
    
    protected function NewRelationSelect($AmountOfParentRows) {
        $Sources = new Relational\ResultSetSources($this->JoinTable);
        $Sources->AddJoins(parent::GetRelationJoins($this->GetRelatedTable()));
        $Select = new Relational\ResultSetSelect(
                new Relational\ResultSetSpecification(
                        $Sources, 
                        new Relational\Criteria($this->JoinTable)));
        
        if($AmountOfParentRows > 1) {
            $Select->AddColumns($this->ParentForeignKey->GetParentColumns());
        }
        
        return $Select;
    }
    
    protected function JoinType() {
        return Relational\JoinType::Inner;
    }
    
    protected function GetRelationJoins(Relational\ITable $Table) {
        $Joins = [new Relational\Join(Relational\JoinType::Left, $this->JoinTable, $this->ParentForeignKey->GetConstraintPredicate())];
        return array_merge($Joins, parent::GetRelationJoins($Table));
    }
    
    protected function AddParentColumnsToSelect(Relational\ResultSetSelect $Select) {
        parent::AddParentColumnsToRequest($Select);
        $Select->AddColumns($this->ParentForeignKey->GetParentColumns());
    }
    
    protected function MapParentRowToRelatedKey(ForeignKey $ForeignKey, Relational\ResultRow $ParentRow) {
        $JoinTableKey = $this->ParentForeignKey->ParentKey();
        $this->ParentForeignKey->MapReferencedToParentKey($ParentRow, $JoinTableKey);
        
        return $JoinTableKey;
    }    
    
    protected function ParentTable(ForeignKey $ForeignKey) { }
    protected function RelatedColumns(ForeignKey $ForeignKey) {
        return [];
    }
    protected function GroupRelatedRowsByParentKeys(array &$MappedRelatedRows, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows) {
        $ParentJoinTableKeys = $this->ParentForeignKey->GetParentColumns();
        $ParentReferencedKeys = $this->ParentForeignKey->GetReferencedColumns();
        
        $GroupedRelatedRows = $this->GroupRowsByColumnValues($RelatedRows, $ParentJoinTableKeys);
        $this->MapParentRowKeysToGroupedRelatedRows($MappedRelatedRows, $ParentRows, $ParentReferencedKeys, $GroupedRelatedRows);
    }
    
    public function MapRelationalParentDataToRelatedData
            (Relational\ColumnData $ParentRow, Relational\ColumnData $RelatedRow) {
        
    }
    
    public function Persist(
            Relational\Transaction $Transaction, 
            Relational\ResultRow $ParentData, 
            array $DiscardedPrimaryKeys, 
            array $PersistedRelatedDataArray) {
        
        $ParentHasForeignKey = $this->ParentForeignKey->HasReferencedKey($ParentData);
        $JoinRows = [];
        $JoinRowsWithoutRelatedForeignKey = [];
        
        foreach($DiscardedPrimaryKeys as $Key => $DiscardedPrimaryKey) {
            $RelatedData = $PersistedRelatedDataArray[$Key];
            
            if($RelatedData !== null) {
                $RelatedHasForiegnKey = $this->ForeignKey->HasReferencedKey($RelatedData);
                $JoinRow = $this->MapRelationshipToJoinRow($ParentData, $RelatedData);
                $Transaction->Persist($JoinRow);
                $JoinRows[] = $JoinRow;
                if(!$RelatedHasForiegnKey) {
                    $JoinRowsWithoutRelatedForeignKey[$Key] = $JoinRow;
                }
            }
            
            if($DiscardedPrimaryKey !== null) {
                $JoinRow = $this->MapRelationshipToJoinRow($ParentData, $DiscardedPrimaryKey);
                $Transaction->Discard($JoinRow->GetPrimaryKey());
            }
        }
        
        $MapForeignKeys = function () use ($ParentHasForeignKey, $ParentData, $JoinRows, $PersistedRelatedDataArray, $JoinRowsWithoutRelatedForeignKey) {
            if(!$ParentHasForeignKey) {
                foreach($JoinRows as $JoinRow) {
                    $this->ParentForeignKey->MapReferencedToParentKey($ParentData, $JoinRow);
                }
            }
            
            foreach($JoinRowsWithoutRelatedForeignKey as $Key => $JoinRowsWithoutRelatedForeignKey) {
                $RelatedData = $PersistedRelatedDataArray[$Key];
                $this->ForeignKey->MapReferencedToParentKey($RelatedData, $JoinRowsWithoutRelatedForeignKey);
            }
        };
        
        $Transaction->SubscribeToPrePersistEvent(
                $this->JoinTable, 
                $MapForeignKeys);
    }
    
    private function MapRelationshipToJoinRow(Relational\ColumnData $ParentData, Relational\ColumnData $RelatedData) {
        if($this->RelatesToTableTwo) {
            return $this->JoinTable->JoinRow($ParentData, $RelatedData);
        }
        else {
            return $this->JoinTable->JoinRow($RelatedData, $ParentData);               
        }
    }
}

?>