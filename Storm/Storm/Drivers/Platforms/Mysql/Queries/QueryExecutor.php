<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Requests;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

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
    
    protected function ExecuteCommit(IConnection $Connection, array $Tables,
            array &$DiscardedRequests, array &$DiscardedPrimaryKeys, 
            array $Operations, array &$PersistedRowGroups) {
        foreach($DiscardedRequests as $Request) {
            $this->DeleteQuery($Connection, $Request)->Execute();
        }
        foreach($DiscardedPrimaryKeys as $PrimaryKey) {
            $this->DeleteQuery($Connection, new Requests\PrimaryKeyRequest($PrimaryKey))->Execute();
        }
        foreach($Operations as $Operation) {
            $this->UpdateQuery($Connection, $Operation)->Execute();
        }
        foreach ($PersistedRowGroups as $TableName => $PersistedRows) {
            $this->SaveQuery($Connection, $Tables[$TableName], $PersistedRows)->Execute();
        }
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
        
        $QueryBuilder->Append(' ON DUPLICATE KEY UPDATE ');
        $First = true;
        foreach($Identifiers as $Identifier) {
            if($First) $First = false;
            else
                $QueryBuilder->Append(', ');
            
            $QueryBuilder->AppendIdentifier('# = VALUES(#)', $Identifier);
        }
        
        return $QueryBuilder->Build();
    }
    
    protected function UpdateQuery(IConnection $Connection, Relational\Operation $Operation) {
        $QueryBuilder = $Connection->QueryBuilder();
        $this->AppendOperation($QueryBuilder, $Operation);
        
        return $QueryBuilder->Build();
    }
    
    protected function DeleteQuery(IConnection $Connection, Relational\Request $Request) {
        $Table = $Request->GetTables();
        $QueryBuilder = $Connection->QueryBuilder();
        
        $QueryBuilder->AppendIdentifier('DELETE FROM #', [$Table->GetName()]);
        $this->AppendRequest($QueryBuilder, $Request);
        
        return $QueryBuilder->Build();
    }
}

?>