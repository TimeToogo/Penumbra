<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\PrimaryKeys;

abstract class QueryExecutor implements IQueryExecutor {
    
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
            
            foreach($Transaction->GetProcedures() as $Procedure) {
                $this->ExecuteUpdate($Connection, $Procedure);
            }
            
            $GroupedPersistedRows = $this->GroupByTableName($Transaction->GetPersistedRows(), $TablesOrderedByPersistingDependency);
            foreach($TablesOrderedByPersistingDependency as $Table) {
                $TableName = $Table->GetName();
                if(isset($GroupedPersistedRows[$TableName])) {
                    $Transaction->TriggerPrePersistEvent($GroupedPersistedRows[$TableName]);
                    $this->PersistRows($Connection, $Transaction, $Table, $GroupedPersistedRows[$TableName]);
                    $Transaction->TriggerPostPersistEvent($GroupedPersistedRows[$TableName]);
                }
            }
            
            $Connection->CommitTransaction();
        }
        catch (Exception $Exception) {
            $Connection->RollbackTransaction();
            throw $Exception;
        }
    }
    protected abstract function DeleteWhereQuery(IConnection $Connection, Table $Table, array &$DiscardedRequests);
    protected abstract function DeleteRowsByPrimaryKeysQuery(IConnection $Connection, Table $Table, array &$DiscardedPrimaryKeys);
    protected abstract function ExecuteUpdate(IConnection $Connection, Relational\Procedure &$ProcedureToExecute);
    private function PersistRows(IConnection $Connection, Relational\Transaction $Transaction, 
            Table $Table, array &$RowsToPersist) {
        
        $HasKeyGenerator = $Table->HasKeyGenerator();
        $ValueWithReturningDataKeyGenerator = null;
        if($HasKeyGenerator) {
            $UnkeyedRows = array_filter($RowsToPersist, 
                    function (Relational\Row $Row) { return !$Row->HasPrimaryKey(); });
            
            $KeyGenerator = $Table->GetKeyGenerator();
            $KeyGeneratorMode = $KeyGenerator->GetKeyGeneratorMode();
            
            if($KeyGeneratorMode === PrimaryKeys\KeyGeneratorMode::PreInsert) {
                /* @var $KeyGenerator PrimaryKeys\PreInsertKeyGenerator */
                
                $KeyGenerator->FillPrimaryKeys($Connection, $UnkeyedRows);
            }
            else if($KeyGeneratorMode === PrimaryKeys\KeyGeneratorMode::ValueWithReturningData) {
                /* @var $KeyGenerator PrimaryKeys\ValueWithReturningDataKeyGenerator */
                
                $ValueWithReturningDataKeyGenerator = $KeyGenerator;
            }
        }
        
        $this->SaveRows($Connection, $Table, $RowsToPersist, $ValueWithReturningDataKeyGenerator);
        
        if($HasKeyGenerator) {
            if($KeyGeneratorMode === PrimaryKeys\KeyGeneratorMode::PostInsert) {
                /* @var $KeyGenerator PrimaryKeys\PostInsertKeyGenerator */
                
                $KeyGenerator->FillPrimaryKeys($Connection, $UnkeyedRows);
            }
        }
    }
    protected abstract function SaveRows(IConnection $Connection, Table $Table, array &$RowsToPersist,
            PrimaryKeys\ValueWithReturningDataKeyGenerator $ValueWithReturningDataKeyGenerator = null);
    
    
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