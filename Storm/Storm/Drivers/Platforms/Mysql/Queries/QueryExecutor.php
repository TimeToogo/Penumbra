<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Requests;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Drivers\Base\Relational\PrimaryKeys\ValueWithReturningDataKeyGenerator;

class QueryExecutor extends Queries\QueryExecutor {
    
    protected function DeleteRowsByPrimaryKeysQuery(IConnection $Connection, Table $Table, array &$DiscardedPrimaryKeys) {
        $this->DeleteQuery($Connection, new Requests\PrimaryKeyRequest($DiscardedPrimaryKeys))->Execute();
    }

    protected function DeleteWhereQuery(IConnection $Connection, Table $Table, array &$DiscardedRequests) {
        foreach($DiscardedRequests as $Request) {
            $this->DeleteQuery($Connection, $Request)->Execute();
        }
    }

    protected function ExecuteUpdate(IConnection $Connection, Relational\Procedure &$ProcedureToExecute) {
        $this->UpdateQuery($Connection, $ProcedureToExecute)->Execute();
    }
    
    protected function SaveRows(IConnection $Connection, Table $Table, array &$RowsToPersist,
            ValueWithReturningDataKeyGenerator $ValueWithReturningDataKeyGenerator = null) {
        if($ValueWithReturningDataKeyGenerator !== null) {
            throw new \Exception('Mysql does not support returning data');
        }
        $this->SaveQuery($Connection, $Table, $RowsToPersist)->Execute();
    }
    
    /*
     * TODO: Verify 'ON DUPLICATE KEY' safety with unique constraints in conjunction with a primary key
     * See temporary fix below
     */
    protected function SaveQuery(IConnection $Connection, Table $Table, array $Rows) {
        if(count($Rows) === 0)
            return;
        
        $QueryBuilder = $Connection->QueryBuilder();
        
        $Columns = $Table->GetColumns();
        $PrimaryKeyColumns = $Table->GetPrimaryKeyColumns();
        $ColumnNames = array_keys($Columns);
        $PrimaryKeyColumnNames = array_keys($PrimaryKeyColumns);
        $TableName = $Table->GetName();
        $Identifiers = array();
        foreach($ColumnNames as $ColumnName) {
            $Identifiers[] = [$TableName, $ColumnName];
        }
        $PrimaryKeyIdentifiers = array();
        foreach($PrimaryKeyColumnNames as $ColumnName) {
            $PrimaryKeyIdentifiers[] = [$TableName, $ColumnName];
        }
        
        $QueryBuilder->AppendIdentifier('INSERT INTO #', [$TableName]);
        $QueryBuilder->AppendIdentifiers('(#)', $Identifiers, ',');
        $QueryBuilder->Append(' VALUES ');
        $First = true;
        foreach($Rows as $Row) {
            if($First) $First = false;
            else
                $QueryBuilder->Append(', ');
            
            $QueryBuilder->Append('(');
            $First1 = true;
            foreach($Columns as $Column) {
                if($First1) $First1 = false;
                else
                    $QueryBuilder->Append(', ');
                
                if(isset($Row[$Column])) {
                    $QueryBuilder->AppendColumnData($Column, $Row[$Column]);
                }
                else {
                    $QueryBuilder->Append('DEFAULT');
                }
            }
            $QueryBuilder->Append(')');
        }
        
        $First = true;
        $FirstPrimaryKey = true;
        foreach($Table->GetColumns() as $Column) {
            if($First) {
                $QueryBuilder->Append(' ON DUPLICATE KEY UPDATE ');
                $First = false;
            }
            else
                $QueryBuilder->Append(', ');
            
            $Identifier = [$TableName, $Column->GetName()];
            if($FirstPrimaryKey && $Column->IsPrimaryKey()) {
                /*
                 * Ugly fix/hack to prevent mysql from updating primary key when encountering
                 * a duplicate value on a seperate unique constraint. Sadly Mysql does not support
                 * the more robust 'MERGE' operation. Furthermore there is no clean way to throw a 
                 * conditional runtime error in Mysql reliably.
                 * 
                 * Example: Persisting a account entity with a unique username, if the user changes
                 * their username and and a duplicate username exists, mysql could attempt to update
                 * the other duplicate row with the new values/primary key. This should not be an 
                 * issue as then it will fail with a duplicate primary key but could lead to some 
                 * wacky edge cases that I want no part in.
                 */
                $QueryBuilder->AppendIdentifier('# = IF(', $Identifier);
                $FirstPrimaryKey = true;
                foreach($PrimaryKeyIdentifiers as $PrimaryKeyIdentifier) {
                    if($FirstPrimaryKey) $FirstPrimaryKey = false;
                    else
                        $QueryBuilder->Append(' AND ');
                    
                    $QueryBuilder->AppendIdentifier('# = VALUES(#)', $PrimaryKeyIdentifier);
                }
                $QueryBuilder->Append(',');
                $QueryBuilder->AppendIdentifier('VALUES(#)', $Identifier);
                $QueryBuilder->Append(',');
                $QueryBuilder->Append('(SELECT 1 UNION ALL SELECT 1))');
                
                $FirstPrimaryKey = false;
            }
            else {
                $QueryBuilder->AppendIdentifier('# = VALUES(#)', $Identifier);
            }
        }
        
        return $QueryBuilder->Build();
    }
          
    protected function SelectQuery(QueryBuilder $QueryBuilder, Relational\Request $Request) {
        $QueryBuilder->Append('SELECT ');
        $First = true;
        foreach($Request->GetColumns() as $Column) {
            if($First) $First = false;
            else
                $QueryBuilder->Append (', ');
            
            $QueryBuilder->AppendExpression(Expression::ReviveColumn($Column));
            $QueryBuilder->AppendIdentifier(' AS #', [$Column->GetIdentifier()]);
        }
        
        $QueryBuilder->AppendIdentifiers(' FROM # ', array_keys($Request->GetTables()), ', ');
        $this->AppendCriterion($QueryBuilder, $Request->GetCriterion());
    }
    
    protected function UpdateQuery(IConnection $Connection, Relational\Procedure $Procedure) {
        $QueryBuilder = $Connection->QueryBuilder();
        
        $TableNames = array_map(
                function ($Table) { 
                    return $Table->GetName();
                }, 
                $Procedure->GetTables());
        
        $QueryBuilder->AppendIdentifiers('UPDATE # SET ', $TableNames, ',');
        
        $First = true;
        foreach($Procedure->GetExpressions() as $Expression) {
            if($First) $First = false;
            else
                $QueryBuilder->Append(', ');
            
            $QueryBuilder->AppendExpression($Expression);
        }
        $this->AppendCriterion($QueryBuilder, $Procedure->GetCriterion());
        
        return $QueryBuilder->Build();
    }
    
    protected function DeleteQuery(IConnection $Connection, Relational\Request $Request) {
        $Table = $Request->GetTables();
        $QueryBuilder = $Connection->QueryBuilder();
        
        $QueryBuilder->AppendIdentifier('DELETE FROM # ', [$Table->GetName()]);
        if($Request->GetCriterion()->IsConstrained()) {
            $QueryBuilder->Append('WHERE ');
        }
        $this->AppendCriterion($QueryBuilder, $Request->GetCriterion());
        
        return $QueryBuilder->Build();
    }
    
    private function AppendCriterion(QueryBuilder $QueryBuilder, Relational\Criterion $Criterion) {
        if($Criterion->IsConstrained()) {
            $QueryBuilder->Append('WHERE ');
        }
        $QueryBuilder->AppendCriterion($Criterion);
    }
}

?>