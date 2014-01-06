<?php

namespace Storm\Drivers\Base\Relational;

use \Storm\Core\Relational;

abstract class Database extends Relational\Database {
    private $Platform;
    private $Connection;
    private $DatabaseSyncer;
    private $QueryExecutor;
    
    public function __construct() {
        $this->Platform = $this->Platform();        
        $this->Connection = $this->Platform->GetConnection();
        $this->DatabaseSyncer = $this->Platform->GetDatabaseSyncer();
        $this->QueryExecutor = $this->Platform->GetQueryExecutor();
        
        parent::__construct();
        
        $this->DatabaseSyncer->Sync($this->Connection, $this);
    }
    /**
     * @return IPlatform
     */
    protected abstract function Platform();
    
    /**
     * @return IPlatform
     */
    public function GetPlatform() {
        return $this->Platform;
    }
    
    final protected function GetRows(Relational\Request $Request) {
        return $this->QueryExecutor->Select($this->Connection, $Request);
    }
    
    final public function Commit(Relational\Transaction $Transaction) {
        return $this->QueryExecutor->Commit($this->Connection, 
                $this->GetTablesOrderedByPersistingDependency(),
                $this->GetTablesOrderedByDiscardingDependency(),
                $Transaction);
    }
}

?>
