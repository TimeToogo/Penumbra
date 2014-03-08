<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

abstract class CriterionCompiler implements ICriterionCompiler {
    
    final public function AppendTableDefinition(QueryBuilder $QueryBuilder, Relational\Criterion $Criterion) {
        $Table = $Criterion->GetTable();
        $Joins = $Criterion->IsJoined() ? $Criterion->GetJoins() : null;
        
        $this->AppendTableDefinitionClause($QueryBuilder, $Table, $Joins);
    }

    final public function AppendWhere(QueryBuilder $QueryBuilder, Relational\Criterion $Criterion) {
        if($Criterion->IsConstrained()) {
            $this->AppendWhereClause($QueryBuilder, $Criterion->GetPredicateExpressions());
        }
    }

    final public function AppendOrderBy(QueryBuilder $QueryBuilder, Relational\Criterion $Criterion) {
        if($Criterion->IsOrdered()) {
            $this->AppendOrderByClause($QueryBuilder, $Criterion->GetOrderedExpressionsAscendingMap());
        }
    }
    
    final public function AppendRange(QueryBuilder $QueryBuilder, Relational\Criterion $Criterion) {
        if($Criterion->IsRanged()) {
            $this->AppendGroupByClause($QueryBuilder, $Criterion->GetRangeOffset(), $Criterion->GetRangeAmount());
        }
    }

    protected abstract function AppendTableDefinitionClause(QueryBuilder $QueryBuilder, Relational\ITable $Table, array $Joins = null);
    
    protected abstract function AppendWhereClause(QueryBuilder $QueryBuilder, array $PredicateExpressions);
    
    protected abstract function AppendOrderByClause(QueryBuilder $QueryBuilder, \SplObjectStorage $ExpressionAscendingMap);
    
    protected abstract function AppendRangeClause(QueryBuilder $QueryBuilder, $Offset, $Limit);
}

?>