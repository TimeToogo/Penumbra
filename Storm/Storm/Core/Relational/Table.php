<?php

namespace Storm\Core\Relational;

use \Storm\Core\Containers\Registrar;

/**
 *{@inheritDoc}
 */
abstract class Table implements ITable {
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
    private $ColumnsByIdentifiers;
    
    /**
     * @var IColumn[]
     */
    private $PrimaryKeyColumns;
    
    /**
     * @var string[]
     */
    private $PrimaryKeyColumnByIdentifiers;
    
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
     *{@inheritDoc}
     */
    final public function InitializeStructure(Database $Database) {
        $this->OnInitializeStructure($Database);
        
        $Registrar = new Registrar(IColumn::IColumnType);
        $this->RegisterColumns($Registrar, $Database);
        $this->Columns = array();
        $this->ColumnsByIdentifiers = array();
        $this->PrimaryKeyColumns = array();
        $this->PrimaryKeyColumnByIdentifiers = array();
        foreach($Registrar->GetRegistered() as $Column) {
            $this->AddColumn($Column);
        }
        
        $this->OnStructureInitialized($Database);
    }
    protected function OnInitializeStructure(Database $Database) { }
    protected function OnStructureInitialized(Database $Database) { }
    
    /**
     *{@inheritDoc}
     */
    public abstract function InitializeRelatedStructure(Database $Database);
    
    /**
     *{@inheritDoc}
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
     * @throws InvalidColumnException If the column belongs to another table
     */
    private function AddColumn(IColumn $Column) {
        if($Column->HasTable()) {
            if(!$Column->GetTable()->Is($this)) {
                throw new InvalidColumnException(
                        'The registered column %s is already registered with another table %s.',
                        $Column->GetName(),
                        $Column->GetTable()->GetName());
            }
        }
        $Column->SetTable($this);
        
        $ColumnName = $Column->GetName();
        $ColumnIdentifier = $Column->GetIdentifier();
        $this->Columns[$ColumnName] = $Column;
        $this->ColumnsByIdentifiers[$ColumnIdentifier] = $Column;
        if($Column->IsPrimaryKey()) {
            $this->PrimaryKeyColumns[$ColumnName] = $Column;
            $this->PrimaryKeyColumnByIdentifiers[$ColumnIdentifier] = $Column;
        }
     }

    /**
     *{@inheritDoc}
     */
    final public function GetName() {
        return $this->Name;
    }
    
    /**
     *{@inheritDoc}
     */
    final public function HasColumn($Name) {
        return isset($this->Columns[$Name]);
    }
    
    /**
     *{@inheritDoc}
     */
    final public function HasPrimaryKey($Name) {
        return isset($this->PrimaryKeyColumns[$Name]);
    }
    
    /**
     *{@inheritDoc}
     */
    final public function GetColumn($Name) {
        return $this->HasColumn($Name) ? $this->Columns[$Name] : null;
    }
    
    /**
     *{@inheritDoc}
     */
    final public function GetColumnByIdentifier($Identifier) {
        return isset($this->ColumnsByIdentifiers[$Identifier]) ? $this->ColumnsByIdentifiers[$Identifier] : null;
    }
    
    /**
     *{@inheritDoc}
     */
    final public function GetColumns() {
        return $this->Columns;
    }
    
    /**
     *{@inheritDoc}
     */
    final public function GetColumnsByIdentifier() {
        return $this->ColumnsByIdentifiers;
    }
    
    /**
     *{@inheritDoc}
     */
    final public function GetColumnIdentifiers() {
        return array_keys($this->ColumnsByIdentifiers);
    }
    
    /**
     *{@inheritDoc}
     */
    final public function GetPrimaryKeyColumns() {
        return $this->PrimaryKeyColumns;
    }
    
    /**
     *{@inheritDoc}
     */
    final public function GetPrimaryKeyColumnsByIdentifier() {
        return $this->PrimaryKeyColumnByIdentifiers;
    }
    
    /**
     *{@inheritDoc}
     */
    final public function GetPrimaryKeyColumnIdentifiers() {
        return array_keys($this->PrimaryKeyColumnByIdentifiers);
    }
    
    /**
     *{@inheritDoc}
     */
    final public function GetToOneRelations() {
        return $this->ToOneRelations;
    }
    
    /**
     *{@inheritDoc}
     */
    final public function GetToManyRelations() {
        return $this->ToManyRelations;
    }    
    
    /**
     *{@inheritDoc}
     */
    final public function GetPersistingOrderBetween(ITable $OtherTable) {
        return $this->GetDepedencyOrderBetweenInternal(true, $OtherTable);
    }
    
    /**
     *{@inheritDoc}
     */
    final public function GetDiscardingOrderBetween(ITable $OtherTable) {
        return $this->GetDepedencyOrderBetweenInternal(false, $OtherTable);
    }
    
    /**
     *{@inheritDoc}
     */
    final public function GetDepedencyOrderBetween($DependencyMode, ITable $OtherTable) {
        return $this->GetDepedencyOrderBetweenInternal($DependencyMode === DependencyMode::Persisting, $OtherTable);
    }
    
    private function GetDepedencyOrderBetweenInternal($ForPersising, ITable $OtherTable, $Reversed = false) {
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
     *{@inheritDoc}
     */
    final public function Row(array $Data = array()){
        if($this->Row === null) {
            $this->Row = new Row($this);
        }
        return $this->Row->Another($Data);
    }
    
    private $PrimaryKey = null;
    /**
     *{@inheritDoc}
     */
    final public function PrimaryKey(array $Data = array()) {
        if($this->PrimaryKey === null) {
            $this->PrimaryKey = new PrimaryKey($this);
        }
        return $this->PrimaryKey->Another($Data);
    }
   
    /**
     *{@inheritDoc}
     */
    final public function Is(ITable $Table) {
        return $this->Name === $Table->Name;
    }
}

?>