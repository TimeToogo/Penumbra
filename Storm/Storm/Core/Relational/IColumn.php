<?php

namespace Storm\Core\Relational;

interface IColumn {
    const IColumnType = __CLASS__;
    
    public function GetIdentifier();
    
    public function HasTable();
    
    /**
     * @return Table
     */
    public function GetTable();
    
    public function SetTable(Table $Table = null);
    
    public function GetName();
    public function SetName($Name);
    
    public function Retrieve(ColumnData $Data);
    public function Store(ColumnData $Data, $Value);
}

?>