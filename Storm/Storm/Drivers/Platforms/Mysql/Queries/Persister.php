<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Platforms\Base\Queries;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Requests;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Drivers\Base\Relational\PrimaryKeys\ReturningDataKeyGenerator;
use \Storm\Drivers\Base\Relational\PrimaryKeys\PostIndividualInsertKeyGenerator;

class Persister extends Queries\StandardPersister {
    public function __construct($BatchSize = 1000) {
        parent::__construct($BatchSize);
    }
    
    protected function UpsertRowsQuery(
            QueryBuilder $QueryBuilder, 
            Table $Table, 
            array $Rows, 
            $ShouldReturnKeyData) {
        
        if($ShouldReturnKeyData) {
            throw new \Exception('Mysql does not support returning data');
        }
        
        $Columns = $Table->GetColumns();
        $PrimaryKeyColumns = $Table->GetPrimaryKeyColumns();
        $ColumnNames = array_keys($Columns);
        $PrimaryKeyColumnNames = array_keys($PrimaryKeyColumns);
        $TableName = $Table->GetName();
        
        $PrimaryKeyIdentifiers = array();
        foreach($PrimaryKeyColumnNames as $ColumnName) {
            $PrimaryKeyIdentifiers[] = [$TableName, $ColumnName];
        }
        
        $QueryBuilder->AppendIdentifier('INSERT INTO #', [$TableName]);
        $QueryBuilder->AppendIdentifiers('(#)', $ColumnNames, ',');
        
        $QueryBuilder->Append(' SELECT ');
        foreach($QueryBuilder->Iterate($Columns, ', ') as $Column) {
            $QueryBuilder->AppendExpression(Expression::PersistData(Expression::Column($Column)));
        }
        $QueryBuilder->Append(' FROM (');
        
        $Identifiers = array_map(function($Column) { return $Column->GetIdentifier(); }, $Columns);
        $ColumnDatas = array_map(function ($Row) { return $Row->GetColumnData(); }, $Rows);
        
        $First = true;
        foreach($QueryBuilder->Iterate($ColumnDatas, ' UNION ALL SELECT ') as $ColumnData) {
            $FirstValue = true;
            foreach($Identifiers as $Identifier) {
                if($FirstValue) $FirstValue = false;
                else 
                    $QueryBuilder->Append (',');
                
                $QueryBuilder->AppendSingleValue($ColumnData[$Identifier]);
                
                if($First) {
                    $QueryBuilder->AppendIdentifier(' AS #', $ColumnNames);
                }
            }
            $First = false;
        }
        $QueryBuilder->Append(')');
        
        $this->AppendOnDuplicateKeyUpdate($QueryBuilder, $TableName, $Columns, $PrimaryKeyIdentifiers);
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
                foreach($QueryBuilder->Iterate($PrimaryKeyIdentifiers, ' AND ') as $PrimaryKeyIdentifier) {
                    
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
}

?>