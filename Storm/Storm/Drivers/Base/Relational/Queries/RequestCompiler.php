<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;
use \Storm\Core\Relational\Request;
use \Storm\Core\Relational\Operation;
use \Storm\Core\Object\Constraints\Predicate;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

abstract class RequestCompiler implements IRequestCompiler {    
    final public function AppendOperation(QueryBuilder $QueryBuilder, Operation $Operation) {
        $this->AppendOperationStatement($QueryBuilder, $Operation);
        $this->AppendOperationExpressions($QueryBuilder, $Operation->GetExpressions());
        $this->AppendRequest($QueryBuilder, $Operation);
    }
    protected abstract function AppendOperationStatement(QueryBuilder $QueryBuilder, Operation $Operation);
    
    protected abstract function AppendOperationExpressions(QueryBuilder $QueryBuilder, array $Expressions);
    
    final protected function AppendOperationExpression(QueryBuilder $QueryBuilder, Expression $Expression) {
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