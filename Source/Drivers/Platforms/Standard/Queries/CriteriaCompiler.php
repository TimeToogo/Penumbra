<?php

namespace Penumbra\Drivers\Platforms\Standard\Queries;

use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Platforms\Base\Queries;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;
use \Penumbra\Drivers\Base\Relational\Queries\ParameterType;

class CriteriaCompiler extends Queries\CriteriaCompiler {
    
    protected function AppendWhereClause(QueryBuilder $QueryBuilder, array $PredicateExpressions) {
        $QueryBuilder->Append(' WHERE (');
        foreach($QueryBuilder->Delimit($PredicateExpressions, ' AND ') as $PredicateExpression) {
            $QueryBuilder->AppendExpression($PredicateExpression);
        }
        $QueryBuilder->Append(')');
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
            $QueryBuilder->AppendValue('LIMIT #', $Limit, ParameterType::Integer);
        }

        $QueryBuilder->Append(' ');
        $QueryBuilder->AppendValue('OFFSET #', $Offset, ParameterType::Integer);
    }
}

?>