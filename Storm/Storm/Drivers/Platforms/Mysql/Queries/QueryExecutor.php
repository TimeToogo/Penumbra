<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Requests;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Drivers\Base\Relational\PrimaryKeys\ValueWithReturningDataKeyGenerator;

class QueryExecutor extends Queries\QueryExecutor {
      
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
        $QueryBuilder->AppendIdentifiers('FROM # ', array_keys($Request->GetTables()), ', ');
    }

    protected function DeleteRowsByPrimaryKeysQuery(IConnection $Connection, Relational\Table $Table, array &$DiscardedPrimaryKeys) {
        $this->DeleteQuery($Connection, new Requests\PrimaryKeyRequest($DiscardedPrimaryKeys))->Execute();
    }

    protected function DeleteWhereQuery(IConnection $Connection, Relational\Table $Table, array &$DiscardedRequests) {
        foreach($DiscardedRequests as $Request) {
            $this->DeleteQuery($Connection, $Request)->Execute();
        }
    }

    protected function ExecuteUpdates(IConnection $Connection, Relational\Table $Table, array &$ProceduresToExecute) {
        foreach($ProceduresToExecute as $Procedure) {
            $this->UpdateQuery($Connection, $Procedure)->Execute();
        }
    }
    
    protected function SaveRows(IConnection $Connection, Relational\Table $Table, array &$RowsToPersist,
            ValueWithReturningDataKeyGenerator $ValueWithReturningDataKeyGenerator = null) {
        if($ValueWithReturningDataKeyGenerator !== null) {
            throw new Exception('Mysql does not support returning data');
        }
        $this->SaveQuery($Connection, $Table, $RowsToPersist)->Execute();
    }
    
    protected function SaveQuery(IConnection $Connection, Relational\Table $Table, array $Rows) {
        if(count($Rows) === 0)
            return;
        
        $QueryBuilder = $Connection->QueryBuilder();
        
        $Columns = $Table->GetColumns();
        $ColumnNames = array_keys($Columns);
        $TableName = $Table->GetName();
        $Identifiers = array();
        foreach($ColumnNames as $ColumnName) {
            $Identifiers[] = [$TableName, $ColumnName];
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
        foreach($Table->GetColumns() as $Column) {
            if(!$Column->IsPrimaryKey()) {
                if($First) {
                    $QueryBuilder->Append(' ON DUPLICATE KEY UPDATE ');
                    $First = false;
                }
                else
                    $QueryBuilder->Append(', ');

                $Identifier = [$TableName, $Column->GetName()];
                $QueryBuilder->AppendIdentifier('# = VALUES(#)', $Identifier);
            }
        }
        
        return $QueryBuilder->Build();
    }
    
    protected function UpdateQuery(IConnection $Connection, Relational\Procedure $Procedure) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->AppendProcedure($QueryBuilder, $Procedure);
        
        return $QueryBuilder->Build();
    }
    
    protected function DeleteQuery(IConnection $Connection, Relational\Request $Request) {
        $Table = $Request->GetTables();
        $QueryBuilder = $Connection->QueryBuilder();
        
        $QueryBuilder->AppendIdentifier('DELETE FROM # ', [$Table->GetName()]);
        $QueryBuilder->AppendRequest($QueryBuilder, $Request);
        
        return $QueryBuilder->Build();
    }
}

?>