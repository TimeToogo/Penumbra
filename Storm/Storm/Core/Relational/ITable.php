<?php

namespace Storm\Core\Relational;

use \Storm\Core\Containers\Registrar;

/**
 * The table represents the structure and relations of a table in the
 * underlying database.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface ITable {
    const ITableType = __CLASS__;
    
    /**
     * Initializes the columns of the table.
     * 
     * @param Database $Database The parent database
     * @return void
     */
    public function InitializeStructure(Database $Database);
    
    /**
     * Initializes the related structure of the table.
     * 
     * @param Database $Database The parent database
     * @return void
     */
    public function InitializeRelatedStructure(Database $Database);
    
    /**
     * Initializes the relations of the table.
     * 
     * @param Database $Database The parent database
     * @return void
     */
    public function InitializeRelations(Database $Database);
    
    /**
     * @return string
     */
    public function GetName();
    
    /**
     * @param string $Name The column name
     * @return boolean
     */
    public function HasColumn($Name);
    
    /**
     * @param string $Name The column name
     * @return boolean
     */
    public function HasPrimaryKey($Name);
    
    /**
     * @param string $Name The column name
     * @return IColumn|null
     */
    public function GetColumn($Name);
    
    /**
     * @param string $Identifier The column identifier
     * @return IColumn|null
     */
    public function GetColumnByIdentifier($Identifier);
    
    /**
     * Gets the table columns, indexed by their respective column name.
     * 
     * @return IColumn[]
     */
    public function GetColumns();
    
    /**
     * @return IColumn[]
     */
    public function GetColumnsByIdentifier();
    
    /**
     * @return string[]
     */
    public function GetColumnIdentifiers();
    
    /**
     * Gets the table columns which are primary keys, 
     * indexed by their respective column name.
     * 
     * @return IColumn[]
     */
    public function GetPrimaryKeyColumns();
    
    /**
     * @return IColumn[]
     */
    public function GetPrimaryKeyColumnsByIdentifier();
    
    /**
     * @return string[]
     */
    public function GetPrimaryKeyColumnIdentifiers();
    
    /**
     * @return IToOneRelation[]
     */
    public function GetToOneRelations();
    
    /**
     * @return IToManyRelation[]
     */
    public function GetToManyRelations();
    
    /**
     * @param ITable $OtherTable The other table
     * @return int The dependency order
     */
    public function GetPersistingOrderBetween(ITable $OtherTable);
    
    /**
     * @param ITable $OtherTable The other table
     * @return int The dependency order
     */
    public function GetDiscardingOrderBetween(ITable $OtherTable);
    
    /**
     * @param ITable $OtherTable The other table
     * @return int The dependency order
     */
    public function GetDepedencyOrderBetween($DependencyMode, ITable $OtherTable);
    
    /**
     * Get a row of this table
     * 
     * @param array $Data The column data
     * @return Row The row
     */
    public function Row(array $Data = []);
    
    /**
     * Get a primary key of this table
     * 
     * @param array $Data The column data
     * @return PrimaryKey The row
     */
    public function PrimaryKey(array $Data = []);
   
    
    public function Is(ITable $Table);
}

?>