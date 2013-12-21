<?php

namespace Storm\Drivers\Base\Relational\Traits;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\RelationalTableTrait;

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
    private $PrimaryColumnNameMap = array();
    private $ForeignColumnNameMap = array();
    
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
            $this->PrimaryColumnNameMap[$PrimaryColumn->GetName()] = $PrimaryColumn;
            
            $ForeignColumn = $ReferencedColumnMap[$PrimaryColumn];
            $this->ForeignColumnNameMap[$ForeignColumn->GetName()] = $ForeignColumn;
            
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
    
    final public function MapPrimaryKey(Relational\ColumnData $PrimaryKey, Relational\ColumnData $ForeignKey) {
        foreach($PrimaryKey as $ColumnName => $Data) {
            if(isset($this->PrimaryColumnNameMap[$ColumnName])) {
                $PrimaryColumn = $this->PrimaryColumnNameMap[$ColumnName];
                $ForeignColumn = $this->ReferencedColumnMap[$PrimaryColumn];
                $ForeignKey[$ForeignColumn] = $Data;
            }
        }
    }
    
    final public function MapForeignKey(Relational\ColumnData $ForeignKey, Relational\ColumnData $PrimaryKey) {
        foreach($ForeignKey as $ColumnName => $Data) {
            if(isset($this->ForeignColumnNameMap[$ColumnName])) {
                $ForeignColumn = $this->ForeignColumnNameMap[$ColumnName];
                $PrimaryColumn = $this->ReferencedColumnMap[$ForeignColumn];
                $PrimaryKey[$PrimaryColumn] = $Data;
            }
        }
    }
}

?>