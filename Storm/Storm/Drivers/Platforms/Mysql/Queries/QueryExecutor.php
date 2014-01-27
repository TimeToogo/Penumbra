<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Platforms\Base\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Requests;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Drivers\Base\Relational\PrimaryKeys\ReturningDataKeyGenerator;

class QueryExecutor extends Queries\QueryExecutor {
    public function __construct() {
        parent::__construct(new Persister());
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
        
        foreach($QueryBuilder->Delimit($PrimaryKeyNames, ', ') as $PrimaryKeyName) {
            $QueryBuilder->AppendIdentifier('#', [$TableName, $PrimaryKeyName]);
        }
        
        $QueryBuilder->Append(') IN (');
        
        foreach($QueryBuilder->Delimit($PrimaryKeys, ',') as $PrimaryKey) {
            $QueryBuilder->Append('(');
            foreach($QueryBuilder->Delimit($PrimaryKeysColumns, ',') as $PrimaryKeysColumn) {
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