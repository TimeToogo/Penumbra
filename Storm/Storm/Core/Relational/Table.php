<?php

namespace Storm\Core\Relational;

use \Storm\Core\Containers\Registrar;

/**
 * The table represents the structure and relations of a table in the
 * underlying database.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Table {
    use \Storm\Core\Helpers\Type;
    
    /**
     * @var string 
     */
    private $Name;
    
    /**
     * @var IColumn[]
     */
    private $Columns;
    
    /**
     * @var string[]
     */
    private $ColumnIdentifiers;
    
    /**
     * @var IColumn[]
     */
    private $PrimaryKeyColumns;
    
    /**
     * @var string[]
     */
    private $PrimaryKeyColumnIdentifiers;
    
    /**
     * @var IToOneRelation[]
     */
    private $ToOneRelations;
    
    /**
     * @var IToManyRelation[]
     */
    private $ToManyRelations;
    
    /**
     * @var IRelation[]
     */
    private $AllRelations;
    
    public function __construct() {
        $this->Name = $this->Name();
    }
    
    /**
     * Initializes the columns of the table.
     * 
     * @param Database $Database The parent database
     * @return void
     */
    final public function InitializeStructure(Database $Database) {
        $this->OnInitializeStructure($Database);
        
        $Registrar = new Registrar(IColumn::IColumnType);
        $this->RegisterColumns($Registrar, $Database);
        $this->Columns = array();
        $this->ColumnIdentifiers = array();
        $this->PrimaryKeyColumns = array();
        $this->PrimaryKeyColumnIdentifiers = array();
        foreach($Registrar->GetRegistered() as $Column) {
            $this->AddColumn($Column);
        }
        
        $this->OnStructureInitialized($Database);
    }
    protected function OnInitializeStructure(Database $Database) { }
    protected function OnStructureInitialized(Database $Database) { }
    
    /**
     * Initializes the related structure of the table.
     * 
     * @param Database $Database The parent database
     * @return void
     */
    public abstract function InitializeRelatedStructure(Database $Database);
    
    /**
     * Initializes the relations of the table.
     * 
     * @param Database $Database The parent database
     * @return void
     */
    final public function InitializeRelations(Database $Database) {
        $this->OnInitializeRelations($Database);
        
        $Registrar = new Registrar(IToOneRelation::IToOneRelationType);
        $this->RegisterToOneRelations($Registrar, $Database);
        $this->ToOneRelations = $Registrar->GetRegistered();
        
        $Registrar = new Registrar(IToManyRelation::IToManyRelationType);
        $this->RegisterToManyRelations($Registrar, $Database);
        $this->ToManyRelations = $Registrar->GetRegistered();
        
        $this->AllRelations = array_merge($this->ToOneRelations, $this->ToManyRelations);
        
        $this->OnRelationsInitialized($Database);
    }
    protected function OnInitializeRelations(Database $Database) { }
    protected function OnRelationsInitialized(Database $Database) { }
    
    /**
     * Get the table name.
     * 
     * @return string
     */
    protected abstract function Name();
    
    /**
     * Gets the table columns.
     * 
     * @return IColumn[]
     */
    protected abstract function RegisterColumns(Registrar $Registrar, Database $Database);
    
    /**
     * Gets the to one relations.
     * 
     * @return IToOneRelations[]
     */
    protected abstract function RegisterToOneRelations(Registrar $Registrar, Database $Database);
    
    /**
     * Gets the to many relations.
     * 
     * @return IToManyRelations[]
     */
    protected abstract function RegisterToManyRelations(Registrar $Registrar, Database $Database);
    
    /**
     * Add a column to the table.
     * 
     * @param IColumn $Column The column to add
     * @throws \Exception If the column belongs to another table
     */
    private function AddColumn(IColumn $Column) {
        if($Column->HasTable()) {
            if(!$Column->GetTable()->Is($this)) {
                throw new \Exception('Column belongs to another table');
            }
        }
        $Column->SetTable($this);
        
        $ColumnName = $Column->GetName();
        $ColumnIdentifier = $Column->GetIdentifier();
        $this->Columns[$ColumnName] = $Column;
        $this->ColumnIdentifiers[$ColumnIdentifier] = $ColumnIdentifier;
        if($Column->IsPrimaryKey()) {
            $this->PrimaryKeyColumns[$ColumnName] = $Column;
            $this->PrimaryKeyColumnIdentifiers[$ColumnIdentifier] = $ColumnIdentifier;
        }
     }

    /**
     * @return string
     */
    final public function GetName() {
        return $this->Name;
    }
    
    /**
     * @param string $Name The column name
     * @return boolean
     */
    final public function HasColumn($Name) {
        return isset($this->Columns[$Name]);
    }
    
    /**
     * @param string $Name The column name
     * @return boolean
     */
    final public function HasPrimaryKey($Name) {
        return isset($this->PrimaryKeyColumns[$Name]);
    }
    
    /**
     * @param string $Name The column name
     * @return IColumn|null
     */
    final public function GetColumn($Name) {
        return $this->HasColumn($Name) ? $this->Columns[$Name] : null;
    }
    
    /**
     * Gets the table columns, indexed by their respective column name.
     * 
     * @return IColumn[]
     */
    final public function GetColumns() {
        return $this->Columns;
    }
    
    /**
     * @return string[]
     */
    final public function GetColumnIdentifiers() {
        return $this->ColumnIdentifiers;
    }
    
    /**
     * Gets the table columns which are primary keys, 
     * indexed by their respective column name.
     * 
     * @return IColumn[]
     */
    final public function GetPrimaryKeyColumns() {
        return $this->PrimaryKeyColumns;
    }
    
    /**
     * @return string[]
     */
    final public function GetPrimaryKeyColumnIdentifiers() {
        return $this->PrimaryKeyColumnIdentifiers;
    }
    
    /**
     * @return IToOneRelation[]
     */
    final public function GetToOneRelations() {
        return $this->ToOneRelations;
    }
    
    /**
     * @return IToManyRelation[]
     */
    final public function GetToManyRelations() {
        return $this->ToManyRelations;
    }    
    
    /**
     * @param Table $OtherTable The other table
     * @return int The dependency order
     */
    final public function GetPersistingOrderBetween(Table $OtherTable) {
        return $this->GetDepedencyOrderBetweenInternal(true, $OtherTable);
    }
    
    /**
     * @param Table $OtherTable The other table
     * @return int The dependency order
     */
    final public function GetDiscardingOrderBetween(Table $OtherTable) {
        return $this->GetDepedencyOrderBetweenInternal(false, $OtherTable);
    }
    
    /**
     * @param Table $OtherTable The other table
     * @return int The dependency order
     */
    final public function GetDepedencyOrderBetween($DependencyMode, Table $OtherTable) {
        return $this->GetDepedencyOrderBetweenInternal($DependencyMode === DependencyMode::Persisting, $OtherTable);
    }
    
    private function GetDepedencyOrderBetweenInternal($ForPersising, Table $OtherTable, $Reversed = false) {
        foreach($this->AllRelations as $Relation) {
            if($Relation->GetTable()->Is($OtherTable)) {
                if($ForPersising)
                    return $Relation->GetPersistingDependencyOrder();
                else
                    return $Relation->GetDiscardingDependencyOrder();
            }
        }
        if($Reversed)
            return null;
        else {
            $DependendencyOrder = $OtherTable->GetDepedencyOrderBetweenInternal($ForPersising, $this, true);
            if($DependendencyOrder === DependencyOrder::After)
                return DependencyOrder::Before;
            else if($DependendencyOrder === DependencyOrder::Before)
                return DependencyOrder::After;
            else
                return null;
        }
    }
    
    private $Row = null;
    /**
     * Get a row of this table
     * 
     * @param array $Data The column data
     * @return Row The row
     */
    final public function Row(array $Data = array()){
        if($this->Row === null) {
            $this->Row = new Row($this);
        }
        return $this->Row->Another($Data);
    }
    
    private $PrimaryKey = null;
    /**
     * Get a primary key of this table
     * 
     * @param array $Data The column data
     * @return PrimaryKey The row
     */
    final public function PrimaryKey(array $Data = array()) {
        if($this->PrimaryKey === null) {
            $this->PrimaryKey = new PrimaryKey($this);
        }
        return $this->PrimaryKey->Another($Data);
    }
   
    
    final public function Is(Table $Table) {
        return $this->Name === $Table->Name;
    }
}

?>