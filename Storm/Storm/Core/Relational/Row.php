<?php

namespace Storm\Core\Relational;

final class Row extends TableColumnData {
    private $PrimaryKeyColumnsAmount;
    private $PrimaryKey;
    public function __construct(Table $Table, array $RowData = array()) {
        parent::__construct($Table, $RowData);
        $this->PrimaryKey = new PrimaryKey($Table, 
                array_intersect_key($RowData,  $Table->GetPrimaryKeyColumnIdentifiers()));
        $this->PrimaryKeyColumnsAmount = count($Table->GetPrimaryKeyColumns());
    }
    
    public function __clone() {
        $this->PrimaryKey = clone $this->PrimaryKey;
    }
    
    protected function AddColumn(IColumn $Column, $Data) {
        parent::AddColumn($Column, $Data);
        if($Column->IsPrimaryKey()) {
            $this->PrimaryKey[$Column] = $Data;
        }
    }
    
    protected function RemoveColumn(IColumn $Column) {
        parent::RemoveColumn($Column);
        if($Column->IsPrimaryKey()) {
            unset($this->PrimaryKey[$Column]);
        }
    }
    
    /**
     * @return boolean
     */
    final public function HasPrimaryKey() {
        return count(array_filter($this->PrimaryKey->GetColumnData(),
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