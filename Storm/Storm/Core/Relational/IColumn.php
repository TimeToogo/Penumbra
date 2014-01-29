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
    
    public function ToPropertyValue($Value);
    
    public function ToPersistenceValue($Value);

}

?>