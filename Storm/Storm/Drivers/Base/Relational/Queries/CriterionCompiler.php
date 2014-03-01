<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;
use \Storm\Core\Relational\Request;
use \Storm\Core\Relational\Procedure;
use \Storm\Core\Object\Constraints\Predicate;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Expressions\SetExpression;

abstract class CriterionCompiler implements ICriterionCompiler {
    
    final public function AppendCriterion(QueryBuilder $QueryBuilder, Relational\Criterion $Criterion) {
        $this->AppendTable($QueryBuilder, $Criterion->GetTable());
                
        if($Criterion->IsJoined()) {
            $this->AppendJoins($QueryBuilder, $Criterion->GetJoins());
        }
        
        if($Criterion->IsConstrained()) {
            $this->AppendPredicateExpressions($QueryBuilder, $Criterion->GetPredicateExpressions());
        }
        
        if($Criterion->IsGrouped()) {
            $this->AppendGroupByExpressions($QueryBuilder, $Criterion->GetGroupByExpressions());
        }
        
        if($Criterion->IsAggregateConstrained()) {
            $this->AppendAggregatePredicateExpressions($QueryBuilder, $Criterion->GetAggregatePredicateExpressions());
        }
        
        if($Criterion->IsOrdered()) {
            $this->AppendOrderByExpressions($QueryBuilder, $Criterion->GetOrderedExpressionsAscendingMap());
        }
        
        if($Criterion->IsRanged()) {
            $this->AppendRange($QueryBuilder, $Criterion->GetRangeOffset(), $Criterion->GetRangeAmount());
        }
    }
    
    protected abstract function AppendTable(QueryBuilder $QueryBuilder, Relational\ITable $Table);
    
    protected abstract function AppendJoins(QueryBuilder $QueryBuilder, array $Joins);
    
    protected abstract function AppendPredicateExpressions(QueryBuilder $QueryBuilder, array $PredicateExpressions);
    
    protected abstract function AppendGroupByExpressions(QueryBuilder $QueryBuilder, array $Expressions);
    
    protected abstract function AppendAggregatePredicateExpressions(QueryBuilder $QueryBuilder, array $Expressions);
    
    protected abstract function AppendOrderByExpressions(QueryBuilder $QueryBuilder, \SplObjectStorage $ExpressionAscendingMap);
    
    protected abstract function AppendRange(QueryBuilder $QueryBuilder, $Offset, $Limit);
}

?>