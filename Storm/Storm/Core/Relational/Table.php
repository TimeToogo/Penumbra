<?php

namespace Storm\Core\Relational;

use \Storm\Core\Containers\Registrar;

abstract class Table {
    use \Storm\Core\Helpers\Type;
    
    private $Name;
    private $Columns;
    private $PrimaryKeyColumns;
    private $ToOneRelations;
    private $ToManyRelations;
    private $AllRelations;
    
    public function __construct() {
        $this->Name = $this->Name();
    }
    
    final public function InitializeStructure(Database $Context) {
        $this->OnInitializeStructure($Context);
        
        $Registrar = new Registrar(IColumn::IColumnType);
        $this->RegisterColumns($Registrar, $Context);
        $this->Columns = array();
        foreach($Registrar->GetRegistered() as $Column) {
            $this->AddColum($Column);
        }
        
        $this->OnStructureInitialized($Context);
    }
    protected function OnInitializeStructure(Database $Context) { }
    protected function OnStructureInitialized(Database $Context) { }
    
    public abstract function InitializeRelatedStructure(Database $Context);
    
    final public function InitializeRelations(Database $Context) {
        $this->OnInitializeRelations($Context);
        
        $Registrar = new Registrar(IToOneRelation::IToOneRelationType);
        $this->RegisterToOneRelations($Registrar, $Context);
        $this->ToOneRelations = $Registrar->GetRegistered();
        
        $Registrar = new Registrar(IToManyRelation::IToManyRelationType);
        $this->RegisterToManyRelations($Registrar, $Context);
        $this->ToManyRelations = $Registrar->GetRegistered();
        
        $this->AllRelations = array_merge($this->ToOneRelations, $this->ToManyRelations);
        
        $this->OnRelationsInitialized($Context);
    }
    protected function OnInitializeRelations(Database $Context) { }
    protected function OnRelationsInitialized(Database $Context) { }
    
    protected abstract function Name();
    protected abstract function RegisterColumns(Registrar $Registrar, Database $Context);
    protected abstract function RegisterToOneRelations(Registrar $Registrar, Database $Context);
    protected abstract function RegisterToManyRelations(Registrar $Registrar, Database $Context);

    final protected function AddColum(IColumn $Column) {
        if($Column->HasTable()) {
            throw new Exception('Column belongs to another table');
        }
        $Column->SetTable($this);
        
        $this->Columns[$Column->GetName()] = $Column;
    }
    
    final public function GetName() {
        return $this->Name;
    }
    
    final public function HasColumn($Name) {
        return isset($this->Columns[$Name]);
    }
    final public function HasPrimaryKey($Name) {
        if(!isset($this->Columns[$Name]))
            return false;
        else
            return $this->ColumnIsPrimaryKey($this->Columns[$Name]);
    }
    protected abstract function ColumnIsPrimaryKey(IColumn $Column);
    /**
     * @param string $Name
     * @return IColumn
     */
    final public function GetColumn($Name) {
        return $this->HasColumn($Name) ? $this->Columns[$Name] : null;
    }
    /**
     * @return IColumn[]
     */
    final public function GetColumns() {
        return $this->Columns;
    }
    /**
     * @return IColumn[]
     */
    final public function GetPrimaryKeyColumns() {
        if(!isset($this->PrimaryKeyColumns)) {
            $this->PrimaryKeyColumns = array_filter($this->Columns, 
                    function (IColumn $Column) { return $this->ColumnIsPrimaryKey($Column); });
        }
        return $this->PrimaryKeyColumns;
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
    
    
    
    final public function GetPersistingOrderBetween(Table $OtherTable) {
        return $this->GetDepedencyOrderBetweenInternal(true, $OtherTable);
    }
    
    final public function GetDiscardingOrderBetween(Table $OtherTable) {
        return $this->GetDepedencyOrderBetweenInternal(false, $OtherTable);
    }
    
    final public function GetDepedencyOrderBetween($DependencyMode, Table $OtherTable) {
        return $this->GetDepedencyOrderBetweenInternal($DependencyMode === DependencyMode::Persisting, $OtherTable);
    }
    
    private function GetDepedencyOrderBetweenInternal($ForPersising, Table $OtherTable, $Reversed = false) {
        foreach($this->AllRelations as $Relation) {
            if($Relation->GetTables()->Is($OtherTable)) {
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
    
    /**
     * @param array $Data
     * @return Row
     */
    final public function Row(array $Data = array()){
        return new Row($this, $Data);
    }
    
    /**
     * @param array $Data
     * @return PrimaryKey
     */
    final public function PrimaryKey(array $Data = array()){
        return new PrimaryKey($this, $Data);
    }
    
    /**
     * @param array $Data
     * @return Request
     */
    final public function Request($IsSingleRow = false){
        return new Request($this, $IsSingleRow);
    }
    
    final public function Is(self $Table) {
        return $this->Name === $Table->Name;
    }
}

?>