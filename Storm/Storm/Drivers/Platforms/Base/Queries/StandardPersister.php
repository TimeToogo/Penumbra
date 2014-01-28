<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Base\Relational\Queries\ParameterType;
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
            array &$Rows, 
            callable $PostIndividualInsertCallback) {
        
        $TableName = $Table->GetName();
        $Columns = $Table->GetColumns();
        $ColumnNames = array_keys($Columns);
        
        $QueryBuilder = $Connection->QueryBuilder();
        $this->AppendInsert($QueryBuilder, $TableName, $ColumnNames);
        
        $QueryBuilder->Append('(');
        foreach ($QueryBuilder->Delimit($Columns, ',') as $Column) {
            $QueryBuilder->AppendExpression(Expression::PersistData($Column, Expression::Constant(null)));
        }
        $QueryBuilder->Append(')');
        
        $PreparedInsert = $QueryBuilder->Build();
        $Bindings = $PreparedInsert->GetBindings();
        
        $ColumnValues = array_values($Columns);
        $ParameterTypes = $this->GetParamterTypes($ColumnValues);
        
        foreach($Rows as &$Row) {
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
    
    final protected function UpsertRows(IConnection $Connection, Table $Table, array &$Rows, $ShouldReturnKeyData) {
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
            array &$Rows, 
            $ShouldReturnKeyData);
    
    final public function AppendDataAsDerivedTable(
            QueryBuilder $QueryBuilder, 
            array $Columns,
            $DerivedTableName,
            array &$ColumnDataArray) {
        
        $QueryBuilder->Append(' SELECT ');
        /*
         * Apply all the persisting data expressions as a select on the derived table
         * rather than on every row
         */
        foreach($QueryBuilder->Delimit($Columns, ', ') as $ColumnName => $Column) {
            $QueryBuilder->AppendExpression(
                    Expression::PersistData($Column, Expression::Identifier([$DerivedTableName, $ColumnName])));
        }
        
        $QueryBuilder->Append(' FROM (');
        
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
    
    private function GetParamterTypes(array $Columns) {
        return array_combine(array_keys($Columns), array_map(
                function($Column) {
                    return $Column->GetDataType()->GetParameterType();
                },
                $Columns));
    }
}

?>