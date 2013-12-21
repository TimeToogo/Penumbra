<?php

namespace Storm\Core\Relational;

class ResultRow extends ColumnData {
    private $Tables = array();
    private $Columns = array();
    private $TableColumnsMap = array();
    public function __construct(array $Columns, array $ColumnData = array(), $VerifiedData = false) {
        foreach($Columns as $Column) {
            $Table = $Column->GetTable();
            $TableName = $Table->GetName();
            $ColumnIdentifier = $Column->GetIdentifier();
            
            $this->Tables[$TableName] = $Table;
            $this->Columns[$ColumnIdentifier] = $Column;
            if(!isset($this->TableColumnsMap[$TableName])) {
                $this->TableColumnsMap[$TableName] = array();
            }
            $this->TableColumnsMap[$TableName][$ColumnIdentifier] = true;
        }
        
        parent::__construct($ColumnData, $VerifiedData);
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
    protected function AddColumn($ColumnIdentifier, $Data) {
        if(!isset($this->Columns[$ColumnIdentifier])) {
            throw new \InvalidArgumentException('$Column must be a valid column of the tables: ' . 
                    implode(', ', array_map(function ($Table) { return $Table->GetName(); }, $this->Tables)));
        }
        parent::AddColumn($ColumnIdentifier, $Data);
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
}

?>