<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Platforms\Base\Queries;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Requests;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Drivers\Base\Relational\PrimaryKeys\ReturningDataKeyGenerator;
use \Storm\Drivers\Base\Relational\PrimaryKeys\PostIndividualInsertKeyGenerator;

abstract class StandardPersister extends BasePersister {
    
    protected function InsertRowsIndividually(
            IConnection $Connection, 
            Table $Table, 
            array $Rows, 
            callable $PostIndividualInsertCallback) {
        
        $TableName = $Table->GetName();
        $Columns = $Table->GetColumns();
        $ColumnNames = array_keys($Columns);
        
        $QueryBuilder = $Connection->QueryBuilder();
        $this->AppendInsert($QueryBuilder, $TableName, $ColumnNames);
        
        $QueryBuilder->AppendAllColumnData(reset($Rows), ',');
        
        $PreparedInsert = $QueryBuilder->Build();
        $Bindings = $PreparedInsert->GetBindings();
        
        $ColumnValues = array_values($Columns);
        $ParameterTypes = array_map(function($Column) {
            $Column->GetDataType()->GetParameterType();
        });
        
        foreach($Rows as $Row) {
            foreach($ColumnValues as $Count => $Column) {
                if(isset($Row[$Column])) {
                    $Bindings->Bind($Row[$Column], $ParameterTypes[$Count], $Count);
                }
                else {
                    $Bindings->Bind(null, \Storm\Drivers\Base\Relational\Queries\ParameterType::Null, $Count);
                }
            }
            $PreparedInsert->Execute();
            $PostIndividualInsertCallback($Row);
        }
    }
    
    protected function AppendInsert(QueryBuilder $QueryBuilder, $TableName, $ColumnNames) {
        $QueryBuilder->AppendIdentifier('INSERT INTO #', [$TableName]);
        
        $QueryBuilder->AppendIdentifiers('(#) VALUES ', $ColumnNames, ',');
    }
    
    final protected function UpsertRows(IConnection $Connection, Table $Table, array $Rows, $ShouldReturnKeyData) {
        $QueryBuilder = $Connection->QueryBuilder();
        
        $this->UpsertRowsQuery($QueryBuilder, $Table, $Rows, $ShouldReturnKeyData);
        
        $ExecutedQuery = $QueryBuilder->Build()->Execute();
        if($ShouldReturnKeyData) {
            return $ExecutedQuery->FetchAll();
        }
    }
    protected abstract function UpsertRowsQuery(
            QueryBuilder $QueryBuilder, 
            Table $Table, 
            array $Rows, 
            $ShouldReturnKeyData);
}

?>