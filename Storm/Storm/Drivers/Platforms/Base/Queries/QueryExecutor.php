<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Requests;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Drivers\Base\Relational\PrimaryKeys\ReturningDataKeyGenerator;

abstract class QueryExecutor extends Queries\QueryExecutor {
    private $SaveRowBatchSize = null;
    
    public function __construct($SaveRowBatchSize = null) {
        $this->SaveRowBatchSize = $SaveRowBatchSize;
    }
    
    final protected function DeleteRowsByPrimaryKeysQuery(IConnection $Connection, Table $Table, array &$DiscardedPrimaryKeys) {
        if(count($DiscardedPrimaryKeys) === 0) {
            return;
        }
        $QueryBuilder = $Connection->QueryBuilder();
        $this->DeletePrimaryKeysQuery($QueryBuilder, $Table, $DiscardedPrimaryKeys);
        $QueryBuilder->Build()->Execute();
    }

    final protected function DeleteWhereQuery(IConnection $Connection, Relational\Criterion $DiscardedRequest) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->DeleteQuery($QueryBuilder, $DiscardedRequest);
        $QueryBuilder->Build()->Execute();
    }

    final protected function ExecuteUpdate(IConnection $Connection, Relational\Procedure &$ProcedureToExecute) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->UpdateQuery($QueryBuilder, $ProcedureToExecute);
        $QueryBuilder->Build()->Execute();
    }
    
    final protected function SaveRows(IConnection $Connection, Table $Table, array &$RowsToPersist,
            ReturningDataKeyGenerator $ValueWithReturningDataKeyGenerator = null) {
        if(count($RowsToPersist) === 0) {
            return;
        }
        $SaveRowBatches = array();
        if($this->SaveRowBatchSize === null) {
            $SaveRowBatches[] = $RowsToPersist;
        }
        else {
            $SaveRowBatches = array_chunk($RowsToPersist, $this->SaveRowBatchSize);
        }
        foreach($SaveRowBatches as $SaveRowBatch) {
            $QueryBuilder = $Connection->QueryBuilder();
            $this->SaveQuery($QueryBuilder, $Table, $SaveRowBatch, $ValueWithReturningDataKeyGenerator);
            $QueryBuilder->Build()->Execute();
        }
    }
    
    protected abstract function SaveQuery(QueryBuilder $QueryBuilder, Table $Table, array $Rows,
            ReturningDataKeyGenerator $ValueWithReturningDataKeyGenerator = null);
    
    protected abstract function UpdateQuery(QueryBuilder $QueryBuilder, Relational\Procedure $Procedure);
    
    protected abstract function DeleteQuery(QueryBuilder $QueryBuilder, Relational\Criterion $Criterion);
    
    protected abstract function DeletePrimaryKeysQuery(QueryBuilder $QueryBuilder, Table $Table, array $PrimaryKeys);
}

?>