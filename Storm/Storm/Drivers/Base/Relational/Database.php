<?php

namespace Storm\Drivers\Base\Relational;

use \Storm\Core\Relational;

abstract class Database extends Relational\Database {
    private $Platform;
    private $Connection;
    private $DatabaseSyncer;
    private $QueryExecutor;
    private $ToOneRelationReviver;
    private $ToManyRelationReviver;
    
    public function __construct() {
        $this->Platform = $this->Platform();        
        $this->Connection = $this->Platform->GetConnection();
        $this->DatabaseSyncer = $this->Platform->GetDatabaseSyncer();
        $this->QueryExecutor = $this->Platform->GetQueryExecutor();
        $this->ToOneRelationReviver = $this->Platform->GetToOneRelationReviver();
        $this->ToManyRelationReviver = $this->Platform->GetToManyRelationReviver();
        
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
    
    final protected function ReviveToOneRelation(Relational\IToOneRelation $Relation, array $Rows) {
        return $this->ToOneRelationReviver->Revive($this->Connection,
                $Relation, $Rows);
    }
    
    final protected function ReviveToManyRelation(Relational\IToManyRelation $Relation, array $Rows) {
        return $this->ToManyRelationReviver->Revive($this->Connection, 
                $Relation, $Rows);
    }
    
    final public function GeneratePrimaryKeys(Relational\Table $Table, $Amount = 1) {
        return $Table->GeneratePrimaryKeys($this->Connection, $Amount);
    }
}

?>
