<?php

namespace Storm\Core\Relational;

/**
 * This class represents the data of column from one or many tables.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ResultRow extends ColumnData {
    /**
     * @var ITable[] 
     */
    private $Tables = [];
    
    /**
     * @var Row[] 
     */
    private $Rows = [];
    
    /**
     * @var PrimaryKey[] 
     */
    private $PrimaryKeys = [];
    
    public function __construct(array $Columns, array $ColumnData = []) {
        foreach($Columns as $Column) {
            $Table = $Column->GetTable();
            $TableName = $Table->GetName();
            
            $this->Tables[$TableName] = $Table;
            if(!isset($this->Rows[$TableName])) {
                $this->Rows[$TableName] = $Table->Row(array_intersect_key($ColumnData, $Table->GetColumnIdentifiers()));
                $this->PrimaryKeys[$TableName] = $this->Rows[$TableName]->GetPrimaryKey();
            }
        }
        
        parent::__construct($Columns, $ColumnData);
    }
    
    public function GetData() {
        $Data = [];
        foreach($this->Rows as $Row) {
            $Data += $Row->GetData();
        }
        
        return $Data;
    }
    
    public function SetData(array $Data) {
        parent::SetData($Data);
        foreach($this->Rows as $Row) {
            $Row->SetData($Data);
        }
    }
    
    protected function AddColumnData(IColumn $Column, $Data) {
        parent::AddColumnData($Column, $Data);
        
        $this->Rows[$Column->GetTable()->GetName()]->AddColumnData($Column, $Data);
    }
    
    protected function RemoveColumnData(IColumn $Column) {
        parent::RemoveColumnData($Column);
        
        $this->Rows[$Column->GetTable()->GetName()][$Column]->RemoveColumnData($Column);
    }
    
    protected function GetColumnData(IColumn $Column) {
        return $this->Rows[$Column->GetTable()->GetName()]->GetColumnData($Column);
    }    
    
    protected function HasColumnData(IColumn $Column) {
        return $this->Rows[$Column->GetTable()->GetName()]->HasColumnData($Column);
    }
    
    public function __clone() {
        foreach($this->Rows as $TableName => $Rows) {
            $this->Rows[$TableName] = clone $this->Rows[$TableName];
            $this->PrimaryKeys[$TableName] = $this->Rows[$TableName]->GetPrimaryKey();
        }
    }
    
    /**
     * @return ITable[]
     */
    final public function GetTables() {
        return $this->Tables;
    }
    
    final public function IsOf(ITable $Table) {
        return isset($this->Tables[$Table->GetName()]);
    }
    
    /**
     * @return Row[]
     */
    final public function GetRows() {
        return $this->Rows;
    }
    
    /**
     * @return PrimaryKeys[]
     */
    final public function GetPrimaryKeys() {
        return $this->PrimaryKeys;
    }
    
    private function InvalidTable(ITable $Table) {
        return new InvalidTableException(
                    'The supplied table is not part of this result row, expecting on of %s: %s given',
                    implode(', ', array_keys($this->Rows)),
                    $Table->GetName());
    }
    
    /**
     * Get a the row of the supplied table
     * 
     * @param ITable $Table The table of the row to retreive
     * @return Row The matching row
     * @throws InvalidTableException If the table is not part of this result row
     */
    final public function GetRow(ITable $Table) {
        if(!$this->IsOf($Table)) {
            throw $this->InvalidTable($Table);
        }
        
        return $this->Rows[$Table->GetName()];
    }
    
    /**
     * Get a the primary key of the supplied table
     * 
     * @param ITable $Table The table of the primary key to retreive
     * @return PrimaryKey The matching primary key
     * @throws InvalidTableException If the table is not part of this result row
     */
    final public function GetPrimaryKey(ITable $Table) {
        if(!$this->IsOf($Table)) {
            throw $this->InvalidTable($Table);
        }
        
        return $this->PrimaryKeys[$Table->GetName()];
    }
    
    /**
     * Gets the data from the supplied columns
     * 
     * @param IColumn[] $Columns The columns to get the from
     * @return ResultRow The data of the supplied columns
     */
    final public function GetDataFromColumns(array $Columns) {
        return new ResultRow($Columns, $this->GetData());
    }
    
    
    /**
     * Gets the data from the supplied columns from all of the supplied result rows
     * NOTE: Keys are preserved.
     * 
     * @param ResultRow[] $ResultRows The result rows to get the data from
     * @param IColumn[] $Columns The columns to get the from
     * @return ResultRow[] The data of the supplied columns
     */
    final public static function GetAllDataFromColumns(array $ResultRows, array $Columns) {
        $NewResultRow = new ResultRow($Columns);
        
        $NewResultRows = [];
        foreach($ResultRows as $Key => $ResultRow) {
            $NewResultRows[$Key] = $NewResultRow->Another($ResultRow->GetData());
        }
        
        return $NewResultRows;
    }
}

?>