<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

abstract class StandardRowPersister extends UpsertPersister {
    
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
        
        $QueryBuilder->Append('(');
        foreach($QueryBuilder->Delimit($Columns, ',') as $Column) {
            $QueryBuilder->AppendColumnData($Column, null);
        }
        $QueryBuilder->Append(')');
        
        $PreparedInsert = $QueryBuilder->Build();
        $Bindings = $PreparedInsert->GetBindings();
        
        $ColumnValues = array_values($Columns);
        $ParameterTypes = $this->GetParamterTypes($ColumnValues);
        
        foreach($Rows as $Row) {
            foreach($ColumnValues as $Count => $Column) {
                $Value = $Row[$Column];
                $Bindings->Bind($Value, $Value === null ? ParameterType::Null : $ParameterTypes[$Count], $Count);
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
    
    protected function AppendDataAsInlineTable(
            QueryBuilder $QueryBuilder, 
            array $Columns,
            $DerivedTableName,
            array $ColumnDataArray) {
        
        $QueryBuilder->Append(' SELECT ');
        
        /*
         * Apply all the persisting data expressions as a select on the inline table
         * rather than on every row
         */
        foreach($QueryBuilder->Delimit($Columns, ', ') as $ColumnName => $Column) {
            $QueryBuilder->AppendExpression(
                    Expression::PersistData($Column, Expression::Identifier([$DerivedTableName, $ColumnName])));
        }

        $QueryBuilder->Append(' FROM (');

        $ColumnDataArray = array_map(
                function(Relational\ColumnData $ColumnData) { 
                    return $ColumnData->GetData();
                }, 
                $ColumnDataArray);

        $ColumnNames = array_map(function($Column) { return $Column->GetName(); }, $Columns);
        $Identifiers = array_combine($ColumnNames, 
                array_map(function($Column) { return $Column->GetIdentifier(); }, $Columns));
        $ParameterTypes = $this->GetParamterTypes($Columns);

        $First = true;
        $QueryBuilder->Append('SELECT ');
        foreach($QueryBuilder->Delimit($ColumnDataArray, ' UNION ALL SELECT ') as $ColumnData) {
            $FirstValue = true;
            foreach($Identifiers as $ColumnName => $Identifier) {
                if($FirstValue) $FirstValue = false;
                else 
                    $QueryBuilder->Append(',');

                $Value = isset($ColumnData[$Identifier]) ? $ColumnData[$Identifier] : null;
                $QueryBuilder->AppendSingleValue($Value, $Value === null ? ParameterType::Null : $ParameterTypes[$ColumnName]);

                if($First) {
                    $QueryBuilder->AppendIdentifier(' AS #', [$ColumnName]);
                }
            }
            $First = false;
        }

        $QueryBuilder->AppendIdentifier(') #', [$DerivedTableName]);
    }
    
    protected function AppendDataAsInlineRow(
            QueryBuilder $QueryBuilder, 
            array $Columns, 
            Relational\ColumnData $ColumnData) {
        
        $QueryBuilder->Append(' SELECT ');
        foreach($QueryBuilder->Delimit($Columns, ', ') as $Column) {
            $QueryBuilder->AppendExpression(
                    Expression::PersistData($Column, Expression::Constant($ColumnData[$Column])));
        }
    }
    
    private function GetParamterTypes(array $Columns) {
        return array_combine(array_keys($Columns), array_map(
                function($Column) {
                    return $Column->GetDataType()->GetParameterType();
                },
                $Columns));
    }
    
    protected function DeleteRowBatch(
            IConnection $Connection, 
            Table $Table, 
            array $PrimaryKeys) {
        $QueryBuilder = $Connection->QueryBuilder();
        
        $TableName = $Table->GetName();        
        $DerivedTableName = $TableName . 'PrimaryKeys';
        $TransformedDerivedTableName = $TableName . 'PersistencePrimaryKeys';
        
        $PrimaryKeysColumns = $Table->GetPrimaryKeyColumns();
        $PrimaryKeyNames = array_keys($PrimaryKeysColumns);
        
        $QueryBuilder->AppendIdentifier('DELETE # FROM # INNER JOIN (', [$TableName]);
        
        $this->AppendDataAsInlineTable($QueryBuilder, $PrimaryKeys, $DerivedTableName, $ColumnDataArray);
        
        $QueryBuilder->AppendIdentifier(') #', [$TransformedDerivedTableName]);   
        
        $QueryBuilder->Append(' ON ');
        
        foreach($QueryBuilder->Delimit($PrimaryKeyNames, ' AND ') as $PrimaryKeyName) {
            $QueryBuilder->AppendIdentifier('# = ', [$TableName, $PrimaryKeyName]);
            $QueryBuilder->AppendIdentifier('#', [$TransformedDerivedTableName, $PrimaryKeyName]);
        }
        
        $QueryBuilder->Build()->Execute();
    }
    
    protected function AppendDeletePrimaryKeyData(
            QueryBuilder $QueryBuilder,
            array $PrimaryKeysColumns, 
            $DerivedTableName, 
            array $PrimaryKeys) {
        $this->AppendDataAsInlineTable(
                $QueryBuilder,
                $PrimaryKeysColumns, 
                $DerivedTableName, 
                $PrimaryKeys);
    }
}

?>