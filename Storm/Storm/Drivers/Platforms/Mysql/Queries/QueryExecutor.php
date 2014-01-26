<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Platforms\Base\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Requests;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Drivers\Base\Relational\PrimaryKeys\ValueWithReturningDataKeyGenerator;

class QueryExecutor extends Queries\QueryExecutor {
    const SaveRowBatchSize = 2000;
    
    public function __construct() {
        parent::__construct(self::SaveRowBatchSize);
    }
    
    /*
     * TODO: Verify 'ON DUPLICATE KEY' safety with unique constraints in conjunction with a primary key
     * See temporary fix below
     */
    protected function SaveQuery(QueryBuilder $QueryBuilder, Table $Table, array $Rows,
            ValueWithReturningDataKeyGenerator $ValueWithReturningDataKeyGenerator = null) {
        if($ValueWithReturningDataKeyGenerator !== null) {
            throw new \Exception('Mysql does not support returning data');
        }
        
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
            
            $this->AppendRow($QueryBuilder, $Columns, $Row);
        }
        
        $this->AppendOnDuplicateKeyUpdate($QueryBuilder, $TableName, $Columns, $PrimaryKeyIdentifiers);
    }
    
    private function AppendRow(QueryBuilder $QueryBuilder, array $Columns, Relational\Row $Row) {
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
    
    private function AppendOnDuplicateKeyUpdate(
            QueryBuilder $QueryBuilder, $TableName, 
            array $Columns, array $PrimaryKeyIdentifiers) {
        $First = true;
        $FirstPrimaryKey = true;
        foreach($Columns as $Column) {
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
    
    protected function UpdateQuery(QueryBuilder $QueryBuilder, Relational\Procedure $Procedure) {        
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
    }
    
    protected function DeleteQuery(QueryBuilder $QueryBuilder, Relational\Criterion $Criterion) {
        $QueryBuilder->AppendIdentifiers('DELETE # FROM # ', array_keys($Criterion->GetTables()), ',');
        
        $this->AppendCriterion($QueryBuilder, $Criterion);
    }
    
    protected function DeletePrimaryKeysQuery(QueryBuilder $QueryBuilder, Table $Table, array $PrimaryKeys) {
        $TableName = $Table->GetName();
        $QueryBuilder->AppendIdentifier('DELETE # FROM # WHERE ', [$TableName]);
        
        $PrimaryKeysColumns = $Table->GetPrimaryKeyColumns();
        $PrimaryKeyNames = array_keys($PrimaryKeysColumns);
        $QueryBuilder->Append('(');
        
        foreach($QueryBuilder->Iterate($PrimaryKeyNames, ', ') as $PrimaryKeyName) {
            $QueryBuilder->AppendIdentifier('#', [$TableName, $PrimaryKeyName]);
        }
        
        $QueryBuilder->Append(') IN (');
        
        foreach($QueryBuilder->Iterate($PrimaryKeys, ',') as $PrimaryKey) {
            $QueryBuilder->Append('(');
            foreach($QueryBuilder->Iterate($PrimaryKeysColumns, ',') as $PrimaryKeysColumn) {
                if(isset($PrimaryKey[$PrimaryKeysColumn])) {
                    $QueryBuilder->AppendColumnData($PrimaryKeysColumn, $PrimaryKey[$PrimaryKeysColumn]);
                }
                else {
                    throw new \Exception();
                }
            }
            $QueryBuilder->Append(')');
        }
        
        $QueryBuilder->Append(')');
    }
    
    private function AppendCriterion(QueryBuilder $QueryBuilder, Relational\Criterion $Criterion) {
        if($Criterion->IsConstrained()) {
            $QueryBuilder->Append('WHERE ');
        }
        $QueryBuilder->AppendCriterion($Criterion);
    }
}

?>