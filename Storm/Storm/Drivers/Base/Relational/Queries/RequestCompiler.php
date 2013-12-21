<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;
use \Storm\Core\Relational\Request;
use \Storm\Core\Relational\Operation;
use \Storm\Core\Object\Constraints\Predicate;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

abstract class RequestCompiler implements IRequestCompiler {
    private $ExpressionCompiler;
    private $PredicateCompiler;
    public function __construct(IExpressionCompiler $ExpressionCompiler, 
            IPredicateCompiler $PredicateCompiler) {
        $this->ExpressionCompiler = $ExpressionCompiler;
        $this->PredicateCompiler = $PredicateCompiler;
    }
    
    final public function AppendOperation(QueryBuilder $QueryBuilder, Operation $Operation) {
        $this->AppendOperationStatement($QueryBuilder, $Operation);
        $this->AppendOperationExpressions($QueryBuilder, $Operation->GetExpressions());
        $this->AppendRequest($QueryBuilder, $Operation);
    }
    protected abstract function AppendOperationStatement(QueryBuilder $QueryBuilder, Operation $Operation);
    
    protected abstract function AppendOperationExpressions(QueryBuilder $QueryBuilder, array $Expressions);
    
    final protected function AppendOperationExpression(QueryBuilder $QueryBuilder, Expression $Expression) {
        $this->ExpressionCompiler->Append($QueryBuilder, $Expression);
    }
    
    final public function AppendRequest(QueryBuilder $QueryBuilder, Request $Request) {
        $Table = $Request->GetTables();
        if($Request->IsConstrained()) {
            $this->AppendPredicates($QueryBuilder, $Table, $Request->GetPredicates());
        }
        if($Request->IsOrdered()) {
            $this->AppendOrderedColumns($QueryBuilder, $Table, $Request->GetOrderedColumnsAscendingMap());
        }
        if($Request->IsRanged()) {
            $this->AppendRange($QueryBuilder, $Request->GetRangeOffset(), $Request->GetRangeAmount());
        }
    }
    
    protected abstract function AppendPredicates(QueryBuilder $QueryBuilder, Relational\Table $Table, array $Predicates);
    
    final protected function AppendPredicate(QueryBuilder $QueryBuilder, Predicate $Predicate) {
        $this->PredicateCompiler->Append($QueryBuilder, $Predicate);
    }
    
    protected abstract function AppendOrderedColumns(QueryBuilder $QueryBuilder, Relational\Table $Table, \SplObjectStorage $ColumnAscendingMap);
    
    protected abstract function AppendRange(QueryBuilder $QueryBuilder, $Offset, $Limit);
}

?>