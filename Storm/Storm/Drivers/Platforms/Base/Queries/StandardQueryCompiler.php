<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

class StandardQueryCompiler implements Queries\IQueryCompiler {
    
    public function AppendSelect(QueryBuilder $QueryBuilder, Relational\Select $Select) {
        switch ($Select->GetSelectType()) {
            
            case Relational\SelectType::ResultSet:
                return $this->AppendResultSetSelect($QueryBuilder, $Select);
            
            case Relational\SelectType::Count:
                return $this->AppendCountSelect($QueryBuilder, $Select);
            
            case Relational\SelectType::Exists:
                return $this->AppendExistsSelect($QueryBuilder, $Select);
            
            default:
                throw new \Storm\Drivers\Base\Relational\PlatformException(
                        'Cannot compile select of type %s: unknown select type %s',
                        get_class($Select),
                        $Select->GetSelectType());
        }
    }
    
    protected function AppendResultSetSelect(QueryBuilder $QueryBuilder, Relational\ResultSetSelect $ResultSetSelect) {
        $QueryBuilder->Append('SELECT ');
        foreach($QueryBuilder->Delimit($ResultSetSelect->GetColumns(), ',') as $Column) {
            $QueryBuilder->AppendExpression(Expression::ReviveColumn($Column));
            $QueryBuilder->AppendIdentifier(' AS #', [$Column->GetIdentifier()]);
        }
        
        $this->AppendSelectClauses($QueryBuilder, $ResultSetSelect);
    }
    
    protected function AppendCountSelect(QueryBuilder $QueryBuilder, Relational\ValueSelect $ValueSelect) {
        $QueryBuilder->Append('SELECT COUNT(*)');
        
        $this->AppendSelectClauses($QueryBuilder, $ValueSelect);
    }
    
    protected function AppendExistsSelect(QueryBuilder $QueryBuilder, Relational\ValueSelect $ValueSelect) {
        $QueryBuilder->Append('SELECT EXISTS (SELECT *');
        $this->AppendSelectClauses($QueryBuilder, $ValueSelect);
        $QueryBuilder->Append(')');
    }
    
    protected function AppendSelectClauses(QueryBuilder $QueryBuilder, Relational\Select $Select) {
        $QueryBuilder->Append(' FROM ');
        $QueryBuilder->AppendTableDefinition($Select->GetCriterion());
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