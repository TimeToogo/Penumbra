<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

abstract class QueryExecutor implements IQueryExecutor {
    
    final public function Select(IConnection $Connection, Relational\Request $Request) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->SelectQuery($QueryBuilder, $Request);
        $QueryBuilder->AppendRequest($Request);
        
        return $Connection->LoadRows($Request->GetTables(), $QueryBuilder->Build());
    }
    protected abstract function SelectQuery(QueryBuilder $QueryBuilder, Relational\Request $Request);
    
    final public function Commit(IConnection $Connection, 
            array $TablesOrderedByPersistingDependency, 
            array $TablesOrderedByDiscardingDependency, 
            Relational\Transaction $Transaction) {
        
        try {
            $Connection->BeginTransaction();
            
            $DiscardedRequests = $this->OrderByTableDependency($Transaction->GetDiscardedRequests(), $TablesOrderedByDiscardingDependency);
            
            $DiscardedPrimaryKeys = $this->OrderByTableDependency($Transaction->GetDiscardedPrimaryKeys(), $TablesOrderedByDiscardingDependency);
            
            $Operations = $this->OrderByTableDependency($Transaction->GetOperations(), $TablesOrderedByPersistingDependency);
            
            $PersistedRows = $this->OrderByTableDependency($Transaction->GetPersistedRows(), $TablesOrderedByPersistingDependency);
            $PersistedRowGroups = $this->GroupRowsByTable($PersistedRows);
            
            $Tables = array_combine(
                    array_map(function ($Table) {
                        return $Table->GetName();
                    }, $TablesOrderedByPersistingDependency), 
                    $TablesOrderedByPersistingDependency);
            $this->ExecuteCommit($Connection, $Tables, $DiscardedRequests, $DiscardedPrimaryKeys, $Operations, $PersistedRowGroups);
            
            $Connection->CommitTransaction();
        }
        catch (Exception $Exception) {
            $Connection->RollbackTransaction();
            throw $Exception;
        }
    }
    
    /**
     * @return IQuery
     */
    protected abstract function ExecuteCommit(IConnection $Connection, array $Tables,
            array &$DiscardedRequests, array &$DiscardedPrimaryKeys, array $Operations, array &$PersistedRowGroups);
    
    
    final protected function AppendOperation(QueryBuilder $QueryBuilder, Relational\Operation $Operation) {
        $this->RequestCompiler->AppendOperation($QueryBuilder, $Operation);
    }
    
    private function GroupRowsByTable(array $Rows) {
        $GroupedRows = array();
        foreach($Rows as $Row) {
            $TableName = $Row->GetTables()->GetName();
            if(!isset($GroupedRows[$TableName])) {
                $GroupedRows[$TableName] = array();
            }
            
            $GroupedRows[$TableName][] = $Row;
        }
        
        return $GroupedRows;
    }
    
    private function OrderByTableDependency(array $ObjectsWithTable, array $OrderedTables) {
        $OrderedObjects = array();
        foreach($OrderedTables as $Table) {
            foreach ($ObjectsWithTable as $Key => $ObjectWithTable) {
                if($ObjectWithTable->GetTables()->Is($Table)) {
                    $OrderedObjects[$Key] = $ObjectWithTable;
                    unset($ObjectsWithTable[$Key]);
                }
            }
        }
        
        return $OrderedObjects;
    }
}

?>