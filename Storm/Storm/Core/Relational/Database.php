<?php

namespace Storm\Core\Relational;

use \Storm\Core\Containers\Registrar;

abstract class Database {
    use \Storm\Core\Helpers\Type;
    
    private $Tables = array();
    private $TablesOrderedByPersistingDependency = array();
    private $TablesOrderedByDiscardingDependency = array();
    
    public function __construct() {
        $Registrar = new Registrar(Table::GetType());
        $this->RegisterTables($Registrar);
        $this->AddTables($Registrar->GetRegistered());
    }
    protected abstract function RegisterTables(Registrar $Registrar);
    
    final protected function AddTables(array $Tables) {
        foreach($Tables as $Key => $Table) {
            $Table->InitializeStructure($this);
            
            unset($Tables[$Key]);
            $Tables[$Table->GetName()] = $Table;
        }
        
        $this->Tables = array_merge($this->Tables, $Tables);
        
        foreach($Tables as $Table) {
            $Table->InitializeRelatedStructure($this);
        }
        foreach($Tables as $Table) {
            $Table->InitializeRelations($this);
        }
        
        foreach($Tables as $Key => $Table) {
            $this->AddTableToOrderedTables($Table, $this->TablesOrderedByPersistingDependency, DependencyMode::Persisting);
            $this->AddTableToOrderedTables($Table, $this->TablesOrderedByDiscardingDependency, DependencyMode::Discarding);
        }
    }
    
    private function AddTableToOrderedTables(Table $Table, array &$OrderedTables, $DependencyMode) {
        $Count = 0;
        foreach($OrderedTables as $OtherTable) {
            if($Table->GetDepedencyOrderBetween($DependencyMode, $OtherTable) === DependencyOrder::Before) {
                array_splice($OrderedTables, $Count, 0, [$Table]);
                return;
            }
            $Count++;
        }
        $OrderedTables[] = $Table;
    }
    
    /**
     * @param string $Name
     * @return bool
     */
    final public function HasTable($Name) {
        return isset($this->Tables[$Name]);
    }
    /**
     * @param string $Name
     * @return Table
     */
    final public function GetTable($Name) {
        return $this->HasTable($Name) ? $this->Tables[$Name] : null;
    }
    
    private function VerifyTable(Table $Table) {
        if(!$this->HasTable($Table->GetName()))
            throw new \InvalidArgumentException('$Table must be of this database');
    }
    
    /**
     * @return Table[]
     */
    final public function GetTables() {
        return $this->Tables;
    }
    
    /**
     * @return Table[]
     */
    public function GetTablesOrderedByPersistingDependency() {
        return $this->TablesOrderedByPersistingDependency;
    }

    /**
     * @return Table[]
     */
    public function GetTablesOrderedByDiscardingDependency() {
        return $this->TablesOrderedByDiscardingDependency;
    }
    
    /**
     * @return ResultRow[]
     */
    final public function Load(Request $Request) {
        foreach($Request->GetTables() as $Table) {
            $this->VerifyTable($Table);
        }
        return $this->GetRows($Request);
    }
    protected abstract function GetRows(Request $Request);
    
    public abstract function Commit(Transaction $Transaction);
}

?>