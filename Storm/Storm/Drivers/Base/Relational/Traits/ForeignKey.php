<?php

namespace Storm\Drivers\Base\Relational\Traits;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\RelationalTableTrait;
use \Storm\Drivers\Base\Relational\Constraints\ForeignKeyPredicate;

final class ForeignKeyMode {
    const NoAction = 0;
    const Cascade = 1;
    const SetNull = 2;
    const Restrict = 3;
}

class ForeignKey extends RelationalTableTrait {
    private $Name;
    private $ParentTable;
    private $ReferencedTable;
    private $ReferencedColumnMap;
    private $ReferencedColumnNameMap = array();
    private $ParentColumnIdentifierMap = array();
    private $ReferencedColumnIdentifierMap = array();
    
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
                    throw new \Exception;//TODO:error message
                }
                if(!$this->ReferencedTable->Is($ReferencedColumn->GetTable())) {
                    throw new \Exception;//TODO:error message
                }                
            }
            
            $this->ParentColumnIdentifierMap[$ParentColumn->GetIdentifier()] = $ParentColumn;
            
            $this->ReferencedColumnIdentifierMap[$ReferencedColumn->GetIdentifier()] = $ReferencedColumn;
            
            $this->ReferencedColumnNameMap[$ParentColumn->GetName()] = $ReferencedColumn->GetName();
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
    
    final public function GetReferencedColumnNameMap() {
        return $this->ReferencedColumnNameMap;
    }
    
    final public function GetUpdateMode() {
        return $this->UpdateMode;
    }

    final public function GetDeleteMode() {
        return $this->DeleteMode;
    }
    
    /**
     * @return Constraints\Predicate
     */
    final public function GetConstraintPredicate() {
        return new ForeignKeyPredicate($this);
    }

    protected function IsRelationalTrait(RelationalTableTrait $OtherTrait) {
        if(!$this->ReferencedTable->Is($OtherTrait->ReferencedTable))
            return false;
        if($this->UpdateMode !== $OtherTrait->UpdateMode || 
                $this->DeleteMode !== $OtherTrait->DeleteMode)
            return false;
        
        return 
            count(array_diff_assoc($this->ReferencedColumnNameMap, $OtherTrait->ReferencedColumnNameMap)) === 0 &&
            count(array_diff_assoc($OtherTrait->ReferencedColumnNameMap, $this->ReferencedColumnNameMap)) === 0; 
    }
    
    final public function MapParentToReferencedKey(Relational\ColumnData $ParentKeyData, Relational\ColumnData $ReferencedKeyData) {
        $MappableColumnValues = array_intersect_key($ParentKeyData->GetColumnData(), $this->ParentColumnIdentifierMap);
        foreach ($MappableColumnValues as $ColumnIdentifier => $ColumnValue) {
            $ParentColumn = $this->ParentColumnIdentifierMap[$ColumnIdentifier];
            $ReferencedColumn = $this->ReferencedColumnMap[$ParentColumn];
            $ReferencedKeyData[$ReferencedColumn] = $ColumnValue;
        }
    }
    
    final public function MapReferencedToParentKey(Relational\ColumnData $ReferencedKeyData, Relational\ColumnData $ParentKeyData) {
        $MappableColumnValues = array_intersect_key($ReferencedKeyData->GetColumnData(), $this->ReferencedColumnIdentifierMap);
        foreach ($MappableColumnValues as $ColumnIdentifier => $ColumnValue) {
            $ReferencedColumn = $this->ReferencedColumnIdentifierMap[$ColumnIdentifier];
            $ParentColumn = $this->ReferencedColumnMap[$ReferencedColumn];
            $ParentKeyData[$ParentColumn] = $ColumnValue;
        }
    }
}

?>