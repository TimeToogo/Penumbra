<?php

namespace Storm\Core\Relational;

/**
 * The row represents the column data from a row in a specific table.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Row extends TableColumnData {
    /**
     * @var int
     */
    private $PrimaryKeyColumnsAmount;
    
    /**
     * @var PrimaryKey
     */
    private $PrimaryKey;
    
    public function __construct(ITable $Table, array $RowData = array()) {
        parent::__construct($Table, $RowData);
        $this->PrimaryKey = $Table->PrimaryKey($RowData);
        $this->PrimaryKeyColumnsAmount = count($Table->GetPrimaryKeyColumns());
    }
    
    public function __clone() {
        $this->PrimaryKey = clone $this->PrimaryKey;
    }
    
    public function GetData() {
        return $this->PrimaryKey->Data + $this->Data;
    }
    
    public function SetData(array $Data) {
        parent::SetData($Data);
        $this->PrimaryKey->SetData($Data);
    }
    
    protected function AddColumnData(IColumn $Column, $Data) {
        parent::AddColumnData($Column, $Data);
        if($Column->IsPrimaryKey()) {
            $this->PrimaryKey->AddColumnData($Column, $Data);
        }
    }
    
    protected function RemoveColumnData(IColumn $Column) {
        parent::RemoveColumn($Column);
        if($Column->IsPrimaryKey()) {
            $this->PrimaryKey->RemoveColumnData($Column);
        }
    }
    
    protected function GetColumnData(IColumn $Column) {
        if($Column->IsPrimaryKey()) {
            return $this->PrimaryKey->GetColumnData($Column);
        }
        return parent::GetColumnData($Column);
    }
    
    protected function HasColumnData(IColumn $Column) {
        if($Column->IsPrimaryKey()) {
            return $this->PrimaryKey->HasColumnData($Column);
        }
        return parent::HasColumnData($Column);
    }
    
    /**
     * Whether or not the row has a complete primary key.
     * 
     * @return boolean
     */
    final public function HasPrimaryKey() {
        return count(array_filter($this->PrimaryKey->GetData(),
                        function ($Value) { return $Value !== null; }))
                === 
                $this->PrimaryKeyColumnsAmount;
    }
    
    /**
     * @return PrimaryKey
     */
    final public function GetPrimaryKey() {
        return $this->PrimaryKey;
    }
}

?>