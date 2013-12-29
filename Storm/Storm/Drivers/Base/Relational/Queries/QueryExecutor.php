<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

abstract class QueryExecutor implements IQueryExecutor {
    
    final public function Select(IConnection $Connection, Relational\Request $Request) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->SelectQuery($QueryBuilder, $Request);
        $QueryBuilder->AppendRequest($Request);
        
        return $Connection->LoadResultRows($Request->GetColumns(), $QueryBuilder->Build());
    }
    protected abstract function SelectQuery(QueryBuilder $QueryBuilder, Relational\Request $Request);
    
    final public function Commit(IConnection $Connection, 
            array $TablesOrderedByPersistingDependency, 
            array $TablesOrderedByDiscardingDependency, 
            Relational\Transaction $Transaction) {
        
        try {
            $Connection->BeginTransaction();
            
            $GroupedDiscardedRequests = $this->GroupByTableName($Transaction->GetDiscardedRequests());
            $GroupedDiscardedPrimaryKeys = $this->GroupByTableName($Transaction->GetDiscardedPrimaryKeys());
            foreach($TablesOrderedByDiscardingDependency as $Table) {
                $TableName = $Table->GetName();
                if(isset($GroupedDiscardedRequests[$TableName])) {
                    $this->DeleteWhereQuery($Connection, $Table, $GroupedDiscardedRequests[$TableName]);
                }
                if(isset($GroupedDiscardedPrimaryKeys[$TableName])) {
                    $this->DeleteRowsByPrimaryKeysQuery($Connection, $Table, $GroupedDiscardedPrimaryKeys[$TableName]);
                }
            }
            
            $GroupedProceduresToExecute = $this->GroupByTableName($Transaction->GetProcedures(), $TablesOrderedByPersistingDependency);
            $GroupedPersistedRows = $this->GroupByTableName($Transaction->GetPersistedRows(), $TablesOrderedByPersistingDependency);
            foreach($TablesOrderedByPersistingDependency as $Table) {
                $TableName = $Table->GetName();
                if(isset($GroupedProceduresToExecute[$TableName])) {
                    $this->ExecuteUpdates($Connection, $Table, $GroupedProceduresToExecute[$TableName]);
                }
                if(isset($GroupedPersistedRows[$TableName])) {
                    $this->PersistRows($Connection, $Table, $GroupedPersistedRows[$TableName]);
                }
            }
            
            $Connection->CommitTransaction();
        }
        catch (Exception $Exception) {
            $Connection->RollbackTransaction();
            throw $Exception;
        }
    }
    protected abstract function DeleteWhereQuery(IConnection $Connection, Relational\Table $Table, array &$DiscardedRequests);
    protected abstract function DeleteRowsByPrimaryKeysQuery(IConnection $Connection, Relational\Table $Table, array &$DiscardedPrimaryKeys);
    protected abstract function ExecuteUpdates(IConnection $Connection, Relational\Table $Table, array &$ProceduresToExecute);
    protected abstract function PersistRows(IConnection $Connection, Relational\Table $Table, array &$RowsToPersist);
    
    
    final protected function AppendProcedure(QueryBuilder $QueryBuilder, Relational\Procedure $Operation) {
        $this->RequestCompiler->AppendProcedure($QueryBuilder, $Operation);
    }
    
    private function GroupRowsByTable(array $Rows) {
        $GroupedRows = array();
        foreach($Rows as $Row) {
            $TableName = $Row->GetTable()->GetName();
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
                if($ObjectWithTable->GetTable()->Is($Table)) {
                    $OrderedObjects[$Key] = $ObjectWithTable;
                    unset($ObjectsWithTable[$Key]);
                }
            }
        }
        
        return $OrderedObjects;
    }
    
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