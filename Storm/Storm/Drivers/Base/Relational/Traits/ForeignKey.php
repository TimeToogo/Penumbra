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
    private $PrimaryColumnIdentifierMap = array();
    private $ForeignColumnIdentifierMap = array();
    
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
            $this->PrimaryColumnIdentifierMap[$PrimaryColumn->GetIdentifier()] = $PrimaryColumn;
            
            $ForeignColumn = $ReferencedColumnMap[$PrimaryColumn];
            $this->ForeignColumnIdentifierMap[$ForeignColumn->GetIdentifier()] = $ForeignColumn;
            
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
    
    /**
     * @return Map
     */
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
    
    final public function MapParentKey(Relational\ColumnData $PrimaryKey, Relational\ColumnData $ForeignKey) {
        foreach($PrimaryKey as $ColumnIdentifer => $Data) {
            if(isset($this->PrimaryColumnIdentifierMap[$ColumnIdentifer])) {
                $PrimaryColumn = $this->PrimaryColumnIdentifierMap[$ColumnIdentifer];
                $ForeignColumn = $this->ReferencedColumnMap[$PrimaryColumn];
                $ForeignKey[$ForeignColumn] = $Data;
            }
        }
    }
    
    final public function MapReferencedKey(Relational\ColumnData $ForeignKey, Relational\ColumnData $PrimaryKey) {
        foreach($ForeignKey as $ColumnIdentifer => $Data) {
            if(isset($this->ForeignColumnIdentifierMap[$ColumnIdentifer])) {
                $ForeignColumn = $this->ForeignColumnIdentifierMap[$ColumnIdentifer];
                $PrimaryColumn = $this->ReferencedColumnMap[$ForeignColumn];
                $PrimaryKey[$PrimaryColumn] = $Data;
            }
        }
    }
}

?>