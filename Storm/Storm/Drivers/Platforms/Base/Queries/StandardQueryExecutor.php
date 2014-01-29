<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Platforms\Base\Queries;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Requests;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

abstract class StandardQueryExecutor extends QueryExecutor {
    private $StandardPersister;
    
    public function __construct(StandardPersister $Persister) {
        parent::__construct($Persister);
        
        $this->StandardPersister = $Persister;
    }
    
    protected function SelectQuery(QueryBuilder $QueryBuilder, Relational\Request $Request) {
        $QueryBuilder->Append('SELECT ');
        foreach($QueryBuilder->Delimit($Request->GetColumns(), ',') as $Column) {
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
        
        foreach($QueryBuilder->Delimit($Procedure->GetExpressions(), ',') as $Expression) {
            
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
        $DerivedTableName = $TableName . '__PrimaryKeys';
        
        $PrimaryKeysColumns = $Table->GetPrimaryKeyColumns();
        $PrimaryKeyNames = array_keys($PrimaryKeysColumns);
        
        $QueryBuilder->AppendIdentifier('DELETE # FROM # INNER JOIN (', [$TableName]);        
        $this->StandardPersister->AppendDataAsDerivedTable(
                $QueryBuilder,
                $PrimaryKeysColumns, 
                $DerivedTableName, 
                $PrimaryKeys);
        $QueryBuilder->AppendIdentifier(') #', [$DerivedTableName]);   
        
        $QueryBuilder->Append(' ON ');
        
        foreach($QueryBuilder->Delimit($PrimaryKeyNames, ' AND ') as $PrimaryKeyName) {
            $QueryBuilder->AppendIdentifier('# = ', [$TableName, $PrimaryKeyName]);
            $QueryBuilder->AppendIdentifier('#', [$DerivedTableName, $PrimaryKeyName]);
        }

    }
    
    protected function AppendCriterion(QueryBuilder $QueryBuilder, Relational\Criterion $Criterion) {
        if($Criterion->IsConstrained()) {
            $QueryBuilder->Append('WHERE ');
        }
        $QueryBuilder->AppendCriterion($Criterion);
    }
}

?>