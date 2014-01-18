<?php

namespace Storm\Core\Relational;

/**
 * This interface represents a column in a table.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IColumn {
    const IColumnType = __CLASS__;
    
    /**
     * The value identifier for the column.
     * 
     * @return string The identifier
     */
    public function GetIdentifier();
    
    /**
     * Whether or not the column is a primary key
     * 
     * @return boolean
     */
    public function IsPrimaryKey();    
    
    /**
     * @return Table|null
     */
    public function GetTable();
    
    /**
     * @return boolean
     */
    public function HasTable(); 
    
    /**
     * Set the parent table.
     * 
     * @param Table|null $Table The parent table
     * @return void
     */
    public function SetTable(Table $Table = null);
    
    /**
     * Gets the name of the column.
     * 
     * @return string
     */
    public function GetName();
    
    /**
     * Sets the name of the column.
     * 
     * @param string $Name The column name
     * @return void
     */
    public function SetName($Name);
    
    /**
     * Gets the column value from the supplied column data
     * 
     * @param ColumnData $Data The column data to retreive from
     * @return mixed
     */
    public function Retrieve(ColumnData $Data);
    
    /**
     * Set the column value of the supplied column data with the supplied value
     * 
     * @param ColumnData $Data The column data to retreive from
     * @param mixed $Value The value to set
     * @return void
     */
    public function Store(ColumnData $Data, $Value);
}

?>