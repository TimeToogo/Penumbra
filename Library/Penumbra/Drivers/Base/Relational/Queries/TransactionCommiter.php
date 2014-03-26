<?php

namespace Penumbra\Drivers\Base\Relational\Queries;

use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Table;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys;

class TransactionCommiter implements ITransactionCommiter {
    /**
     * @var IRowPersister 
     */
    private $RowPersister;
    
    public function __construct(IRowPersister $RowPersister) {
        $this->RowPersister = $RowPersister;
    }

    final public function Commit(IConnection $Connection, 
            array $TablesOrderedByPersistingDependency, 
            array $TablesOrderedByDiscardingDependency, 
            Relational\Transaction $Transaction) {
        
        try {
            $Connection->BeginTransaction();
            
            $GroupedDiscardedPrimaryKeys = $Transaction->GetDiscardedPrimaryKeyGroups();            
            foreach($TablesOrderedByDiscardingDependency as $Table) {
                $TableName = $Table->GetName();
                
                if(isset($GroupedDiscardedPrimaryKeys[$TableName])) {
                    $this->RowPersister->DeleteRows($Connection, $Table, $GroupedDiscardedPrimaryKeys[$TableName]);
                }
            }
            
            foreach($Transaction->GetDeletes() as $Delete) {
                $this->ExecuteDelete($Connection, $Delete);
            }
            
            foreach($Transaction->GetUpdates() as $Update) {
                $this->ExecuteUpdate($Connection, $Update);
            }
            
            $GroupedPersistedRows = $Transaction->GetPersistedRowGroups();
            foreach($TablesOrderedByPersistingDependency as $Table) {
                $TableName = $Table->GetName();
                if(isset($GroupedPersistedRows[$TableName])) {
                    $Transaction->TriggerPrePersistEvent($Table);
                    
                    $this->RowPersister->PersistRows($Connection, $Table, $GroupedPersistedRows[$TableName]);
                    
                    $Transaction->TriggerPostPersistEvent($Table);
                }
            }
            
            $Connection->CommitTransaction();
            
        }
        catch (\Exception $Exception) {
            $Connection->RollbackTransaction();
            throw $Exception;
        }
    }
    
    protected function ExecuteDelete(IConnection $Connection, Relational\Delete $Delete) {
        $QueryBuilder = $Connection->QueryBuilder();
        
        $QueryBuilder->AppendDelete($Delete);
        
        $QueryBuilder->Build()->Execute();
    }
    
    protected function ExecuteUpdate(IConnection $Connection, Relational\Update $Update) {
        $QueryBuilder = $Connection->QueryBuilder();
        
        $QueryBuilder->AppendUpdate($Update);
        
        $QueryBuilder->Build()->Execute();
    }
}

?>