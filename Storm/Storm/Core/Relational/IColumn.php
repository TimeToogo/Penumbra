<?php

namespace Storm\Core\Relational;

interface IColumn {
    const IColumnType = __CLASS__;
    
    public function GetIdentifier();
    public function IsPrimaryKey();    
    
    /**
     * @return Table
     */
    public function GetTable();
    public function HasTable();    
    public function SetTable(Table $Table = null);
    
    public function GetName();
    public function SetName($Name);
    
    public function Retrieve(ColumnData $Data);
    public function Store(ColumnData $Data, $Value);
}

?>