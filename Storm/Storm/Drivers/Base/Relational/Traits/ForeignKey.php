<?php

namespace Storm\Drivers\Base\Relational\Traits;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\RelationalTableTrait;
use \Storm\Drivers\Base\Relational\Expressions\ForeignKeyPredicateExpression;

final class ForeignKeyMode {
    const NoAction = 0;
    const Cascade = 1;
    const SetNull = 2;
}

class ForeignKey extends RelationalTableTrait {
    private $Name;
    private $ParentTable;
    private $ReferencedTable;
    private $ReferencedColumnMap;
    private $ParentReferencedColumnNameMap = [];
    private $ParentColumnIdentifierMap = [];
    private $ReferencedColumnIdentifierMap = [];
    private $ParentReferencedColumnIdentifierMap = [];
    private $ReferencedParentColumnIdentifierMap = [];
    
    private $UpdateMode;
    private $DeleteMode;
    
    public function __construct(
            $Name,
            Map $ReferencedColumnMap,
            $UpdateMode = ForeignKeyMode::NoAction,
            $DeleteMode = ForeignKeyMode::NoAction) {
        
        $this->Name = $Name;
        $this->ReferencedColumnMap = $ReferencedColumnMap;
        foreach($ReferencedColumnMap as $ParentColumn) {
            $ReferencedColumn = $ReferencedColumnMap[$ParentColumn];
            
            if($this->ParentTable === null && $this->ReferencedTable === null) {
                $this->ParentTable = $ParentColumn->GetTable();
                $this->ReferencedTable = $ReferencedColumn->GetTable();
            }
            else {
                if(!$this->ParentTable->Is($ParentColumn->GetTable())) {
                    throw new Relational\InvalidColumnException(
                            'The supplied parent columns must belong to the same table: %s and %s given',
                            $this->ParentTable->GetName(),
                            $ParentColumn->GetTable()->GetName());
                }
                if(!$this->ReferencedTable->Is($ReferencedColumn->GetTable())) {
                    throw new Relational\InvalidColumnException(
                            'The supplied referenced columns must belong to the same table: %s and %s given',
                            $this->ReferencedTable->GetName(),
                            $ReferencedColumn->GetTable()->GetName());
                }                
            }
            $ParentIdentifier = $ParentColumn->GetIdentifier();
            $ReferencedIdentifier = $ReferencedColumn->GetIdentifier();
            
            $this->ParentColumnIdentifierMap[$ParentIdentifier] = $ParentColumn;
            
            $this->ReferencedColumnIdentifierMap[$ReferencedIdentifier] = $ReferencedColumn;
            
            $this->ParentReferencedColumnNameMap[$ParentColumn->GetName()] = $ReferencedColumn->GetName();
            
            $this->ParentReferencedColumnIdentifierMap[$ParentIdentifier] = $ReferencedIdentifier;
            $this->ReferencedParentColumnIdentifierMap[$ReferencedIdentifier] = $ParentIdentifier;
        }
        
        $this->UpdateMode = $UpdateMode;
        $this->DeleteMode = $DeleteMode;
    }
    
    final public function GetName() {
        return $this->Name;
    }

    /**
     * @return Relational\Table
     */
    final public function GetParentTable() {
        return $this->ParentTable;
    }

    /**
     * @return Relational\Table
     */
    final public function GetReferencedTable() {
        return $this->ReferencedTable;
    }
    
    final public function GetParentColumns() {
        return $this->ParentColumnIdentifierMap;
    }
    
    final public function GetReferencedColumns() {
        return $this->ReferencedColumnIdentifierMap;
    }
    
    /**
     * @return Map
     */
    final public function GetReferencedColumnMap() {
        return $this->ReferencedColumnMap;
    }
    
    final public function GetParentReferencedColumnNameMap() {
        return $this->ParentReferencedColumnNameMap;
    }
    
    final public function GetParentColumnIdentifierMap() {
        return $this->ParentColumnIdentifierMap;
    }
    
    final public function GetParentColumnIdentifiers() {
        return array_keys($this->ParentColumnIdentifierMap);
    }

    final public function GetReferencedColumnIdentifierMap() {
        return $this->ReferencedColumnIdentifierMap;
    }

    final public function GetReferencedColumnIdentifiers() {
        return array_keys($this->ReferencedColumnIdentifierMap);
    }

    final public function GetUpdateMode() {
        return $this->UpdateMode;
    }

    final public function GetDeleteMode() {
        return $this->DeleteMode;
    }
    
    private $ParentRow = null;
    /**
     * @return Relational\ResultRow
     */
    final public function ParentKey(array $Data = []) {
        if($this->ParentRow === null) {
            $this->ParentRow = new Relational\ResultRow($this->ParentColumnIdentifierMap);
        }
        return $this->ParentRow->Another($Data);
    }
    
    private $ReferencedRow = null;
    /**
     * @return Relational\ResultRow
     */
    final public function ReferencedKey(array $Data = []) {
        if($this->ReferencedRow === null) {
            $this->ReferencedRow = new Relational\ResultRow($this->ReferencedColumnIdentifierMap);
        }
        return $this->ReferencedRow->Another($Data);
    }
    
    /**
     * @return ForeignKeyPredicateExpression
     */
    final public function GetConstraintPredicate() {
        return new ForeignKeyPredicateExpression($this);
    }

    protected function IsRelationalTrait(RelationalTableTrait $OtherTrait) {
        if(!$this->ReferencedTable->Is($OtherTrait->ReferencedTable))
            return false;
        if($this->UpdateMode !== $OtherTrait->UpdateMode || 
                $this->DeleteMode !== $OtherTrait->DeleteMode)
            return false;
        
        return 
            count(array_diff_assoc($this->ParentReferencedColumnNameMap, $OtherTrait->ParentReferencedColumnNameMap)) === 0 &&
            count(array_diff_assoc($OtherTrait->ParentReferencedColumnNameMap, $this->ParentReferencedColumnNameMap)) === 0; 
    }
    
    final public function HasParentKey(Relational\ColumnData $ParentKeyData) {
        return $this->HasColumnData($ParentKeyData, $this->ParentColumnIdentifierMap);
    }
    
    final public function HasReferencedKey(Relational\ColumnData $ReferencedKeyData) {
        return $this->HasColumnData($ReferencedKeyData, $this->ReferencedColumnIdentifierMap);
    }
    
    private function HasColumnData(Relational\ColumnData $ColumnData, array $ColumnIdentifiersMap) {
        return count($ColumnIdentifiersMap) ===
                count(array_filter(
                        array_intersect_key(
                                $ColumnData->GetData(), 
                                $ColumnIdentifiersMap), 
                        function ($I) { return $I !== null; }));
    }
    
    final public function MapParentToReferencedKey(Relational\ColumnData $ParentKeyData, Relational\ColumnData $ReferencedKeyData) {
        $ParentKeyData = array_intersect_key($ParentKeyData->GetData(), $this->ParentColumnIdentifierMap);
        array_walk($ParentKeyData, function($Value, $Key) use (&$ReferencedKeyData) {
            $ReferencedKeyData[$this->ReferencedColumnIdentifierMap[$this->ParentReferencedColumnIdentifierMap[$Key]]] = $Value;
        });
    }
    
    final public function MapReferencedToParentKey(Relational\ColumnData $ReferencedKeyData, Relational\ColumnData $ParentKeyData) {
        $ReferencedKeyData = array_intersect_key($ReferencedKeyData->GetData(), $this->ReferencedColumnIdentifierMap);
        array_walk($ReferencedKeyData, function($Value, $Key) use (&$ParentKeyData) {
            $ParentKeyData[$this->ParentColumnIdentifierMap[$this->ReferencedParentColumnIdentifierMap[$Key]]] = $Value;
        });
    }
}

?>