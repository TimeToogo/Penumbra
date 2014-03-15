<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

class StandardCriteriaCompiler extends Queries\CriteriaCompiler {
    
    protected function AppendWhereClause(QueryBuilder $QueryBuilder, array $PredicateExpressions) {
        $QueryBuilder->Append(' WHERE (');
        foreach($QueryBuilder->Delimit($PredicateExpressions, ' AND ') as $PredicateExpression) {
            $QueryBuilder->AppendExpression($PredicateExpression);
        }
        $QueryBuilder->Append(')');
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

    protected function AppendOrderByClause(QueryBuilder $QueryBuilder, \SplObjectStorage $ExpressionAscendingMap) {
        $QueryBuilder->Append(' ORDER BY ');
        foreach($QueryBuilder->Delimit($ExpressionAscendingMap, ', ') as $Expression) {
            $Ascending = $ExpressionAscendingMap[$Expression];
            $Direction = $Ascending ? 'ASC' : 'DESC';
            
            $QueryBuilder->AppendExpression($Expression);
            $QueryBuilder->Append(' ' . $Direction);
        }
    }

    protected function AppendRangeClause(QueryBuilder $QueryBuilder, $Offset, $Limit) {
        $QueryBuilder->Append(' ');
        if($Limit === null) {
            $QueryBuilder->Append('LIMIT 18446744073709551615');
        }
        else {
            $QueryBuilder->AppendValue('LIMIT #', $Limit, Queries\ParameterType::Integer);
        }

        $QueryBuilder->Append(' ');
        $QueryBuilder->AppendValue('OFFSET #', $Offset, Queries\ParameterType::Integer);
    }
}

?>