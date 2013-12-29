<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;
use \Storm\Core\Relational\Request;
use \Storm\Core\Relational\Procedure;
use \Storm\Core\Object\Constraints\Predicate;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

abstract class RequestCompiler implements IRequestCompiler {    
    final public function AppendProcedure(QueryBuilder $QueryBuilder, Procedure $Procedure) {
        $this->AppendProcedureStatement($QueryBuilder, $Procedure);
        $this->AppendProcedureExpressions($QueryBuilder, $Procedure->GetExpressions());
        $this->AppendRequest($QueryBuilder, $Procedure);
    }
    protected abstract function AppendProcedureStatement(QueryBuilder $QueryBuilder, Procedure $Procedure);
    
    protected abstract function AppendProcedureExpressions(QueryBuilder $QueryBuilder, array $Expressions);
    
    final protected function AppendProcedureExpression(QueryBuilder $QueryBuilder, Expression $Expression) {
        $QueryBuilder->AppendExpression($Expression);
    }
    
    final public function AppendRequest(QueryBuilder $QueryBuilder, Request $Request) {
        if($Request->IsConstrained()) {
            $this->AppendPredicates($QueryBuilder, $Request->GetPredicates());
        }
        if($Request->IsOrdered()) {
            $this->AppendOrderedColumns($QueryBuilder, $Request->GetOrderedColumnsAscendingMap());
        }
        if($Request->IsRanged()) {
            $this->AppendRange($QueryBuilder, $Request->GetRangeOffset(), $Request->GetRangeAmount());
        }
    }
    
    protected abstract function AppendPredicates(QueryBuilder $QueryBuilder, array $Predicates);
    
    final protected function AppendPredicate(QueryBuilder $QueryBuilder, Predicate $Predicate) {
        $QueryBuilder->AppendPredicate($Predicate);
    }
    
    protected abstract function AppendOrderedColumns(QueryBuilder $QueryBuilder, \SplObjectStorage $ColumnAscendingMap);
    
    protected abstract function AppendRange(QueryBuilder $QueryBuilder, $Offset, $Limit);
}

?>