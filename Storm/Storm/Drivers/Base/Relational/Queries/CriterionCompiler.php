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
        if($Criterion->IsConstrained()) {
            $this->AppendPredicates($QueryBuilder, $Criterion->GetPredicateExpressions());
        }
        if($Criterion->IsOrdered()) {
            $this->AppendOrderedExpressions($QueryBuilder, $Criterion->GetOrderedExpressionsAscendingMap());
        }
        if($Criterion->IsRanged()) {
            $this->AppendRange($QueryBuilder, $Criterion->GetRangeOffset(), $Criterion->GetRangeAmount());
        }
    }
    
    protected abstract function AppendPredicates(QueryBuilder $QueryBuilder, array $Predicates);
    
    final protected function AppendPredicate(QueryBuilder $QueryBuilder, Predicate $Predicate) {
        $QueryBuilder->AppendPredicate($Predicate);
    }
    
    protected abstract function AppendOrderedExpressions(QueryBuilder $QueryBuilder, \SplObjectStorage $ExpressionAscendingMap);
    
    protected abstract function AppendRange(QueryBuilder $QueryBuilder, $Offset, $Limit);
}

?>