<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

class StandardQueryCompiler implements Queries\IQueryCompiler {
    
    protected function DeleteQuery(QueryBuilder $QueryBuilder, Relational\Criterion $Criterion) {
        $QueryBuilder->AppendIdentifiers('DELETE # ', array_keys($Criterion->GetAllTables()), ',');
        
        $this->AppendCriterion($QueryBuilder, $Criterion);
    }
    
    protected function DeletePrimaryKeysQuery(QueryBuilder $QueryBuilder, Table $Table, array $PrimaryKeys) {
        $TableName = $Table->GetName();        
        $DerivedTableName = $TableName . 'PrimaryKeys';
        $TransformedDerivedTableName = $TableName . 'PersistencePrimaryKeys';
        
        $PrimaryKeysColumns = $Table->GetPrimaryKeyColumns();
        $PrimaryKeyNames = array_keys($PrimaryKeysColumns);
        
        $QueryBuilder->AppendIdentifier('DELETE # FROM # INNER JOIN (', [$TableName]);        
        $this->StandardPersister->AppendDataAsInlineTable(
                $QueryBuilder,
                $PrimaryKeysColumns, 
                $DerivedTableName, 
                $PrimaryKeys);
        $QueryBuilder->AppendIdentifier(') #', [$TransformedDerivedTableName]);   
        
        $QueryBuilder->Append(' ON ');
        
        foreach($QueryBuilder->Delimit($PrimaryKeyNames, ' AND ') as $PrimaryKeyName) {
            $QueryBuilder->AppendIdentifier('# = ', [$TableName, $PrimaryKeyName]);
            $QueryBuilder->AppendIdentifier('#', [$TransformedDerivedTableName, $PrimaryKeyName]);
        }

    }
    
    public function AppendSelect(QueryBuilder $QueryBuilder, Relational\Select $Select) {
        $Criterion = $Select->GetCriterion();
        
        $QueryBuilder->Append('SELECT ');
        foreach($QueryBuilder->Delimit($Select->GetColumns(), ',') as $Column) {
            $QueryBuilder->AppendExpression(Expression::ReviveColumn($Column));
            $QueryBuilder->AppendIdentifier(' AS #', [$Column->GetIdentifier()]);
        }
        
        $QueryBuilder->Append(' FROM ');
        $QueryBuilder->AppendTableDefinition($Criterion);
        $this->AppendSelectCriterion($QueryBuilder, $Select);
    }
    
    protected function AppendSelectCriterion(QueryBuilder $QueryBuilder, Relational\Select $Select) {
        $Criterion = $Select->GetCriterion();
        
        $QueryBuilder->AppendWhere($Criterion);
        
        if($Select->IsGrouped()) {
            $this->AppendGroupByClause($QueryBuilder, $Select->GetGroupByExpressions());
        }
        if($Select->IsAggregateConstrained()) {
            $this->AppendHavingClause($QueryBuilder, $Select->GetAggregatePredicateExpressions());
        }
        
        $QueryBuilder->AppendOrderBy($Criterion);
        $QueryBuilder->AppendRange($Criterion);
    }
    
    protected function AppendGroupByClause(QueryBuilder $QueryBuilder, array $Expressions) {
        $QueryBuilder->Append(' GROUP BY ');
        foreach($QueryBuilder->Delimit($Expressions, ', ') as $Expression) {            
            $QueryBuilder->AppendExpression($Expression);
        }
    }
    
    protected function AppendHavingClause(QueryBuilder $QueryBuilder, array $Expressions) {
        $QueryBuilder->Append(' HAVING ');
        foreach($QueryBuilder->Delimit($Expressions, ' AND ') as $Expression) {            
            $QueryBuilder->AppendExpression($Expression);
        }
    }

    public function AppendUpdate(QueryBuilder $QueryBuilder, Relational\Update $Update) {
        $Criterion = $Update->GetCriterion();
        
        $QueryBuilder->Append('UPDATE ');
        $QueryBuilder->AppendTableDefinition($Criterion);
        $QueryBuilder->Append(' SET ');
        
        foreach($QueryBuilder->Delimit($Update->GetExpressions(), ',') as $Expression) {
            $QueryBuilder->AppendExpression($Expression);
        }
        
        $this->AppendCriterion($QueryBuilder, $Criterion);
    }

    public function AppendDelete(QueryBuilder $QueryBuilder, Relational\Delete $Delete) {
        $Criterion = $Delete->GetCriterion();
        
        $QueryBuilder->AppendIdentifiers('DELETE # ', array_keys($Delete->GetTables()), ',');
        $QueryBuilder->Append(' FROM ');
        
        $QueryBuilder->AppendTableDefinition($Criterion);
        
        $this->AppendCriterion($QueryBuilder, $Criterion);
    }
    
    protected function AppendCriterion(QueryBuilder $QueryBuilder, Relational\Criterion $Criterion) {
        $QueryBuilder->AppendWhere($Criterion);
        $QueryBuilder->AppendOrderBy($Criterion);
        $QueryBuilder->AppendRange($Criterion);
    }
}

?>