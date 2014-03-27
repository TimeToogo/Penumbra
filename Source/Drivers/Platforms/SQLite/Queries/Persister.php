<?php

namespace Penumbra\Drivers\Platforms\SQLite\Queries;

use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Table;
use \Penumbra\Drivers\Platforms\Base\Queries;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;
use \Penumbra\Drivers\Base\Relational\Requests;
use \Penumbra\Drivers\Base\Relational\Expressions\Expression;

class Persister extends Queries\StandardRowPersister {
    public function __construct($BatchSize = 1000) {
        parent::__construct($BatchSize);
    }
    
    protected function UpsertRowsQuery(
            QueryBuilder $QueryBuilder, 
            Table $Table, 
            array $Rows, 
            $ShouldReturnKeyData) {
        
        if($ShouldReturnKeyData) {
            throw new \Penumbra\Drivers\Base\Relational\PlatformException('SQLite does not support returning data');
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
        
        $QueryBuilder->AppendIdentifier('INSERT OR REPLACE INTO #', [$TableName]);
        $QueryBuilder->AppendIdentifiers('(#)', $ColumnNames, ',');
        
        //TODO: Fix unique constraint conflicts
        $this->AppendDataAsInlineTable($QueryBuilder, $Table->GetColumns(), $DerivedTableName, $Rows);
    }
}

?>