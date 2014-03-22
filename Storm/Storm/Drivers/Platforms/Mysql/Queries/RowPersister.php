<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Platforms\Standard\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

class RowPersister extends Queries\RowPersister {
    public function __construct($InsertBatchSize = 1000, $DeleteBatchSize = 2000) {
        parent::__construct($InsertBatchSize, $DeleteBatchSize);
    }
    
    protected function AppendInlineData(
            QueryBuilder $QueryBuilder , 
            array $Columns,
            $DerivedTableName,
            array $Data) {
        /*
         * MySQL cannot prepare a statment with an inline table with only a
         * single row. 
         * Bug reported and verified: http://bugs.mysql.com/bug.php?id=71577
         */
        if(count($Data) === 1) {
            $this->AppendDataAsInlineRow($QueryBuilder, $Columns, reset($Data));
        }
        else {
            $this->AppendDataAsInlineTable($QueryBuilder, $Columns, $DerivedTableName, $Data);
        }
    }
    
    protected function UpsertRowsQuery(
            QueryBuilder $QueryBuilder, 
            Relational\ITable $Table, 
            array $Rows, 
            $ShouldReturnKeyData) {
        if($ShouldReturnKeyData) {
            throw new \Storm\Drivers\Base\Relational\PlatformException('Mysql does not support returning data');
        }
        
        $Columns = $Table->GetColumns();
        $PrimaryKeyColumns = $Table->GetPrimaryKeyColumns();
        $ColumnNames = array_keys($Columns);
        $PrimaryKeyColumnNames = array_keys($PrimaryKeyColumns);
        $TableName = $Table->GetName();
        $DerivedTableName = $TableName . 'Values';
        
        $PrimaryKeyIdentifiers = [];
        foreach($PrimaryKeyColumnNames as $ColumnName) {
            $PrimaryKeyIdentifiers[] = [$TableName, $ColumnName];
        }
        
        $QueryBuilder->AppendIdentifier('INSERT INTO #', [$TableName]);
        $QueryBuilder->AppendIdentifiers('(#)', $ColumnNames, ',');
        
        $this->AppendInlineData($QueryBuilder, $Columns, $DerivedTableName, $Rows);
        
        $this->AppendOnDuplicateKeyUpdate($QueryBuilder, $TableName, $Columns, $PrimaryKeyIdentifiers);
    }
    
    private function AppendOnDuplicateKeyUpdate(
            QueryBuilder $QueryBuilder, $TableName, 
            array $Columns, array $PrimaryKeyIdentifiers) {
        
        $QueryBuilder->Append(' ON DUPLICATE KEY UPDATE ');
        
        $FirstPrimaryKey = true;
        foreach($QueryBuilder->Delimit($Columns, ',') as $ColumName => $Column) {
            
            $ColumnIdentifier = [$TableName, $ColumName];
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
                $QueryBuilder->AppendIdentifier('# = IF(', $ColumnIdentifier);
                foreach($QueryBuilder->Delimit($PrimaryKeyIdentifiers, ' AND ') as $PrimaryKeyIdentifier) {
                    $QueryBuilder->AppendIdentifier('# = VALUES(#)', $PrimaryKeyIdentifier);
                }
                $QueryBuilder->Append(',');
                $QueryBuilder->AppendIdentifier('VALUES(#)', $ColumnIdentifier);
                $QueryBuilder->Append(',');
                $QueryBuilder->Append('(SELECT 1 UNION ALL SELECT 1))');
                
                $FirstPrimaryKey = false;
            }
            else {
                $QueryBuilder->AppendIdentifier('# = VALUES(#)', $ColumnIdentifier);
            }
        }
    }
}

?>