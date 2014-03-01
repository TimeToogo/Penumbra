<?php

namespace Storm\Core\Relational;

use \Storm\Core\Containers\Registrar;

/**
 * This is the base class representing the database of the application.
 * The databse represents a group of tables, their columns and relations.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Database {
    use \Storm\Core\Helpers\Type;
        
    /**
     * @var ITable[] 
     */
    private $Tables = [];
    
    /**
     * @var ITable[] 
     */
    private $TablesOrderedByPersistingDependency = [];
    
    /**
     * @var ITable[] 
     */
    private $TablesOrderedByDiscardingDependency = [];
    
    public function __construct() {
        $Registrar = new Registrar(ITable::ITableType);
        $this->RegisterTables($Registrar);
        $this->AddTables($Registrar->GetRegistered());
    }
        
    /**
     * The method to specify the tables in the current database.
     * 
     * @param Registrar $Registrar The registrar to register the tables
     * @return void
     */
    protected abstract function RegisterTables(Registrar $Registrar);
    
    /**
     * Adds an array of tables.
     * 
     * @param ITable[] $Tables The tables to add
     */
    private function AddTables(array $Tables) {
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
    
    /**
     * Adds a table to an array in a specified dependency order.
     * 
     * @param ITable $Table The table to add
     * @param ITable[] $OrderedTables The array to add to
     * @param int $DependencyMode The dependency mode to sort by
     * @return void
     */
    private function AddTableToOrderedTables(ITable $Table, array &$OrderedTables, $DependencyMode) {
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
     * Whether or not a table has been registered.
     * 
     * @param string $Name The name of the table
     * @return boolean
     */
    final public function HasTable($Name) {
        return isset($this->Tables[$Name]);
    }
    
    /**
     * Gets a table by name.
     * 
     * @param string $Name The name of the table
     * @return ITable|null The matching table or null if it has not been registered
     */
    final public function GetTable($Name) {
        return $this->HasTable($Name) ? $this->Tables[$Name] : null;
    }
    
    /**
     * Verifies a table is registered in this database.
     * 
     * @param ITable $Table The table to verify
     * @throws \InvalidArgumentException If the table is not registered
     */
    private function VerifyTable($Method, ITable $Table) {
        if(!$this->HasTable($Table->GetName())) {
            throw new InvalidTableException(
                    'Call to %s with supplied table %s does not belong to this database',
                    $Method,
                    $Table->GetName());
        }
    }
    
    /**
     * @return ITable[]
     */
    final public function GetTables() {
        return $this->Tables;
    }
    
    /**
     * @return ITable[]
     */
    public function GetTablesOrderedByPersistingDependency() {
        return $this->TablesOrderedByPersistingDependency;
    }

    /**
     * @return ITable[]
     */
    public function GetTablesOrderedByDiscardingDependency() {
        return $this->TablesOrderedByDiscardingDependency;
    }
    
    /**
     * Load the rows specified by the request.
     * 
     * @param Request $Request The request to load
     * @return ResultRow[] The loaded result rows
     */
    final public function Load(Request $Request) {
        $Columns = [];
        foreach($Request->GetTables() as $Table) {
            $this->VerifyTable(__METHOD__, $Table);
            $Columns += $Table->GetColumnsIndexedByIdentifier();
        }
        $ResultRowData = $this->LoadResultRowData($Request);
        
        $ResultRow = new ResultRow($Columns, []);
        
        return array_map([$ResultRow, 'Another'], $ResultRowData);
    }
    /**
     * This method should be implemented such that is returns the rows specified
     * by the request from the underlying database.
     * 
     * @param Request $Request The request to load
     * @return array[] The loaded result rows data as an associative array indexed by column identifiers
     */
    protected abstract function LoadResultRowData(Request $Request);
    
    /**
     * Commits the supplied transaction.
     * 
     * @param Transaction $Transaction The transaction to commit
     * @return void
     */
    final public function Commit(Transaction $Transaction) {
        $this->CommitTransaction($Transaction);
    }
    /**
     * This method should be implemented such that it commits the supplied transaction
     * to the underlying database.
     * 
     * @param Transaction $Transaction The transaction to commit
     * @return void
     */
    protected abstract function CommitTransaction(Transaction $Transaction);
}

?>