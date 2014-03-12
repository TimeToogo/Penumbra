<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

abstract class CriteriaCompiler implements ICriteriaCompiler {
    
    final public function AppendTableDefinition(QueryBuilder $QueryBuilder, Relational\Criteria $Criteria) {
        $Table = $Criteria->GetTable();
        $Joins = $Criteria->IsJoined() ? $Criteria->GetJoins() : null;
        
        $this->AppendTableDefinitionClause($QueryBuilder, $Table, $Joins);
    }

    final public function AppendWhere(QueryBuilder $QueryBuilder, Relational\Criteria $Criteria) {
        if($Criteria->IsConstrained()) {
            $this->AppendWhereClause($QueryBuilder, $Criteria->GetPredicateExpressions());
        }
    }

    final public function AppendOrderBy(QueryBuilder $QueryBuilder, Relational\Criteria $Criteria) {
        if($Criteria->IsOrdered()) {
            $this->AppendOrderByClause($QueryBuilder, $Criteria->GetOrderedExpressionsAscendingMap());
        }
    }
    
    final public function AppendRange(QueryBuilder $QueryBuilder, Relational\Criteria $Criteria) {
        if($Criteria->IsRanged()) {
            $this->AppendGroupByClause($QueryBuilder, $Criteria->GetRangeOffset(), $Criteria->GetRangeAmount());
        }
    }

    protected abstract function AppendTableDefinitionClause(QueryBuilder $QueryBuilder, Relational\ITable $Table, array $Joins = null);
    
    protected abstract function AppendWhereClause(QueryBuilder $QueryBuilder, array $PredicateExpressions);
    
    protected abstract function AppendOrderByClause(QueryBuilder $QueryBuilder, \SplObjectStorage $ExpressionAscendingMap);
    
    protected abstract function AppendRangeClause(QueryBuilder $QueryBuilder, $Offset, $Limit);
}

?>