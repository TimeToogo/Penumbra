<?php

namespace Storm\Drivers\Base\Relational;

use \Storm\Core\Relational;

class JoinedRow extends Relational\ColumnData {
    private $JoinedTables;
    private $ColumnTableMap = array();
    private $TableNameColumnMap = array();
    public function __construct(array $JoinedTables, array $ColumnData = array(), $VerifiedData = false) {
        foreach($JoinedTables as $Table) {
            $TableName = $Table->GetName();
            $this->JoinedTables[$TableName] = $Table;
            $Columns = $Table->GetColumns();
            $this->ColumnTableMap = $this->ColumnTableMap + 
                    array_combine(array_keys($Columns), array_fill(0, count($Columns), $Table));
            $this->TableNameColumnMap[$TableName] = array_keys($Columns);
        }
        
        parent::__construct($ColumnData, $VerifiedData);
    }
    
    public function IsOf(Relational\Table $OtherTable) {
        return isset($this->JoinedTables[$OtherTable->GetName()]);
    }
    
    protected function AddColumn($ColumnName, $Data) {
        if(!isset($this->ColumnTableMap[$ColumnName])) {
            throw new \InvalidArgumentException('$ColumnOrColumnName must be a valid column of the tables: ' . 
                    implode(', ', array_map(function ($Table) { return $Table->GetName(); }, $this->JoinedTables)));
        }
        
        parent::AddColumn($ColumnName, $Data);
    }
    
    /**
     * @return Relational\Row
     */
    final public function GetRow(Relational\Table $Table) {
        if(!$this->IsOf($Table))
            throw new \InvalidArgumentException('$Table must be a part of this row');
        
        $ColumnData = $this->GetColumnData();
        $TableColumns = array_flip($this->TableNameColumnMap[$Table->GetName()]);
        $TableColumnData = array_intersect_key($ColumnData, $TableColumns);
        
        return new Relational\Row($Table, $TableColumnData, true);
    }
}

?>