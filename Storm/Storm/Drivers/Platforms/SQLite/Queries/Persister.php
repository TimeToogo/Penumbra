<?php

namespace Storm\Drivers\Platforms\SQLite\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Platforms\Base\Queries;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Requests;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

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
            throw new \Exception('SQLite does not support returning data');
        }
        
        $Columns = $Table->GetColumns();
        $PrimaryKeyColumns = $Table->GetPrimaryKeyColumns();
        $ColumnNames = array_keys($Columns);
        $PrimaryKeyColumnNames = array_keys($PrimaryKeyColumns);
        $TableName = $Table->GetName();
        $DerivedTableName = $TableName . 'Values';
        
        $PrimaryKeyIdentifiers = array();
        foreach($PrimaryKeyColumnNames as $ColumnName) {
            $PrimaryKeyIdentifiers[] = [$TableName, $ColumnName];
        }
        
        $QueryBuilder->AppendIdentifier('INSERT OR REPLACE INTO #', [$TableName]);
        $QueryBuilder->AppendIdentifiers('(#)', $ColumnNames, ',');
        
        //TODO: Fix unique constraint conflicts
        $this->AppendDataAsDerivedTable($QueryBuilder, $Table->GetColumns(), $DerivedTableName, $Rows);
    }
}

?>