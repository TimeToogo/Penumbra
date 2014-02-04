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
     * @return ITable|null
     */
    public function GetTable();
    
    /**
     * @return boolean
     */
    public function HasTable(); 
    
    /**
     * Set the parent table.
     * 
     * @param ITable|null $Table The parent table
     * @return void
     */
    public function SetTable(ITable $Table = null);
    
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