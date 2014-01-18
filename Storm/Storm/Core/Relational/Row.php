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
     * Whether or not the row has a complete primary key.
     * 
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