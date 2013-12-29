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
    private $ReferencedTable;
    private $ReferencedColumnMap;
    private $ReferencedColumnNameMap = array();
    private $ParentColumnIdentifierMap = array();
    private $ReferencedColumnIdentifierMap = array();
    
    private $UpdateMode;
    private $DeleteMode;
    
    public function __construct(
            $Name,
            Relational\Table $ReferencedTable, 
            Map $ReferencedColumnMap,
            $UpdateMode = ForeignKeyMode::NoAction,
            $DeleteMode = ForeignKeyMode::NoAction) {
        
        $this->Name = $Name;
        $this->ReferencedTable = $ReferencedTable;
        $this->ReferencedColumnMap = $ReferencedColumnMap;
        foreach($ReferencedColumnMap as $PrimaryColumn) {
            $this->ParentColumnIdentifierMap[$PrimaryColumn->GetIdentifier()] = $PrimaryColumn;
            
            $ForeignColumn = $ReferencedColumnMap[$PrimaryColumn];
            $this->ReferencedColumnIdentifierMap[$ForeignColumn->GetIdentifier()] = $ForeignColumn;
            
            $this->ReferencedColumnNameMap[$PrimaryColumn->GetName()] = $ForeignColumn->GetName();
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
    final public function GetReferencedTable() {
        return $this->ReferencedTable;
    }
    
    final public function GetParentColumns() {
        return $this->ReferencedColumnMap->GetInstances();
    }
    
    final public function GetReferencedColumns() {
        return $this->ReferencedColumnMap->GetToInstances();
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
        $MappableColumnValues =& array_intersect_key($ParentKeyData->GetColumnData(), $this->ParentColumnIdentifierMap);
        foreach ($MappableColumnValues as $ColumnIdentifier => &$ColumnValue) {
            $ParentColumn = $this->ParentColumnIdentifierMap[$ColumnIdentifier];
            $ReferencedColumn = $this->ReferencedColumnMap[$ParentColumn];
            $ReferencedKeyData[$ReferencedColumn] =& $ColumnValue;
        }
    }
    
    final public function MapReferencedToParentKey(Relational\ColumnData $ReferencedKeyData, Relational\ColumnData $ParentKeyData) {
        $MappableColumnValues =& array_intersect_key($ReferencedKeyData->GetColumnData(), $this->ReferencedColumnNameMap);
        foreach ($MappableColumnValues as $ColumnIdentifier => &$ColumnValue) {
            $ReferencedColumn = $this->ReferencedColumnIdentifierMap[$ColumnIdentifier];
            $ParentColumn = $this->ReferencedColumnMap[$ReferencedColumn];
            $ParentKeyData[$ParentColumn] =& $ColumnValue;
        }
    }
}

?>