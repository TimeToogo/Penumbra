<?php

namespace Storm\Core\Relational;

class ResultRow extends ColumnData {
    private $Tables = array();
    private $TableColumnsMap = array();
    public function __construct(array $Columns, array $ColumnData = array()) {
        foreach($Columns as $Column) {
            $Table = $Column->GetTable();
            $TableName = $Table->GetName();
            $ColumnIdentifier = $Column->GetIdentifier();
            
            $this->Tables[$TableName] = $Table;
            if(!isset($this->TableColumnsMap[$TableName])) {
                $this->TableColumnsMap[$TableName] = array();
            }
            $this->TableColumnsMap[$TableName][$ColumnIdentifier] = true;
        }
        
        parent::__construct($Columns, $ColumnData);
    }
    
    /**
     * @return Table
     */
    final public function GetTables() {
        return $this->Tables;
    }
    
    final public function IsOf(Table $Table) {
        return isset($this->Tables[$Table->GetName()]);
    }
    
    /**
     * @return Row[]
     */
    final public function GetRows() {
        $Rows = array();
        foreach($this->Tables as $Table) {
            $Rows[] = $this->GetRow($Table);
        }
        
        return $Rows;
    }
    
    /**
     * @return Row
     */
    final public function GetRow(Table $Table) {
        if(!$this->IsOf($Table))
            throw new \InvalidArgumentException('$Table must be a part of this row');
        
        $ColumnData = $this->GetColumnData();
        $TableColumnData = array_intersect_key($ColumnData, $this->TableNameColumnMap[$Table->GetName()]);
        
        return new Row($Table, $TableColumnData, true);
    }
    
    /**
     * @return ResultRow
     */
    final public function GetDataFromColumns(array $Columns) {
        $ResultRow = new ResultRow($Columns);
        foreach($Columns as $Column) {
            if(isset($this[$Column])) {
                $ResultRow->SetColumn($Column, $this[$Column]);
            }
        }
        
        return $ResultRow;
    }
}

?>