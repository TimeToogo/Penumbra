<?php

namespace Storm\Core\Relational;

final class Row extends TableColumnData {
    private $PrimaryKeyColumnNames;
    public function __construct(Table $Table, array $RowData = array()) {
        $this->PrimaryKeyColumnNames = array_flip(array_keys($Table->GetPrimaryKeyColumns()));
        parent::__construct($Table, $RowData);
    }
    
    /**
     * @return PrimaryKey
     */
    final public function HasPrimaryKey() {
        return count(array_filter(
                array_intersect_key($this->GetColumnData(),  $this->PrimaryKeyColumnNames),
                        function ($Value) { return $Value !== null; }))
                === 
                count($this->PrimaryKeyColumnNames);
    }
    
    /**
     * @return PrimaryKey
     */
    final public function GetPrimaryKey() {
        $ColumnData = $this->GetColumnData();
        $PrimaryKeyData = array_intersect_key($ColumnData,  $this->PrimaryKeyColumnNames);
        
        return new PrimaryKey($this->GetTable(), $PrimaryKeyData, true);
    }
}

?>