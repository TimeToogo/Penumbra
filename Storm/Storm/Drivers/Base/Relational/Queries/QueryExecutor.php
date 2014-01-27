<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\PrimaryKeys;

abstract class QueryExecutor implements IQueryExecutor {
    /**
     * @var Persister
     */
    private $Persister;
    public function __construct(Persister $Persister) {
        $this->Persister = $Persister;
    }

    
    
    final public function Select(IConnection $Connection, Relational\Request $Request) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->SelectQuery($QueryBuilder, $Request);
        
        return $Connection->LoadResultRows($Request->GetColumns(), $QueryBuilder->Build());
    }
    protected abstract function SelectQuery(QueryBuilder $QueryBuilder, Relational\Request $Request);
    
    final public function Commit(IConnection $Connection, 
            array $TablesOrderedByPersistingDependency, 
            array $TablesOrderedByDiscardingDependency, 
            Relational\Transaction $Transaction) {
        
        try {
            $Connection->BeginTransaction();
            
            $GroupedDiscardedPrimaryKeys = $this->GroupByTableName($Transaction->GetDiscardedPrimaryKeys());
            
            foreach($TablesOrderedByDiscardingDependency as $Table) {
                $TableName = $Table->GetName();
                
                if(isset($GroupedDiscardedPrimaryKeys[$TableName])) {
                    $this->DeleteRowsByPrimaryKeysQuery($Connection, $Table, $GroupedDiscardedPrimaryKeys[$TableName]);
                }
            }
            
            foreach($Transaction->GetDiscardedCriteria() as $Criterion) {
                $this->DeleteWhereQuery($Connection, $Criterion);
            }
            
            foreach($Transaction->GetProcedures() as $Procedure) {
                $this->ExecuteUpdate($Connection, $Procedure);
            }
            
            $GroupedPersistedRows = $this->GroupByTableName($Transaction->GetPersistedRows(), $TablesOrderedByPersistingDependency);
            foreach($TablesOrderedByPersistingDependency as $Table) {
                $TableName = $Table->GetName();
                if(isset($GroupedPersistedRows[$TableName])) {
                    $Transaction->TriggerPrePersistEvent($GroupedPersistedRows[$TableName]);
                    
                    $this->Persister->PersistRows($Connection, $Table, $GroupedPersistedRows[$TableName]);
                    
                    $Transaction->TriggerPostPersistEvent($GroupedPersistedRows[$TableName]);
                }
            }
            
            $Connection->CommitTransaction();
        }
        catch (\Exception $Exception) {
            $Connection->RollbackTransaction();
            throw $Exception;
        }
    }
    protected abstract function DeleteWhereQuery(IConnection $Connection, Relational\Criterion $DiscardedCriteria);
    protected abstract function DeleteRowsByPrimaryKeysQuery(IConnection $Connection, Table $Table, array &$DiscardedPrimaryKeys);
    protected abstract function ExecuteUpdate(IConnection $Connection, Relational\Procedure &$ProcedureToExecute);
       
    
    private function GroupByTableName(array $ObjectsWithTable) {
        $OrderedObjects = array();
        foreach($ObjectsWithTable as $ObjectWithTable) {
            $TableName = $ObjectWithTable->GetTable()->GetName();
            if(!isset($OrderedObjects[$TableName])) {
                $OrderedObjects[$TableName] = array();
            }
            $OrderedObjects[$TableName][] = $ObjectWithTable;
        }
        
        return $OrderedObjects;
    }
}

?>