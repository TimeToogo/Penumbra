<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

class CriterionCompiler extends Queries\CriterionCompiler {
    protected function AppendPredicates(QueryBuilder $QueryBuilder, array $Predicates) {
        $QueryBuilder->Append('(');
        $First = true;
        foreach($Predicates as $Predicate) {
            if($First) $First = false;
            else
                $QueryBuilder->Append (' AND ');
            
            $QueryBuilder->AppendExpression($Predicate);
        }
        $QueryBuilder->Append(')');
    }

    protected function AppendOrderedExpressions(QueryBuilder $QueryBuilder, \SplObjectStorage $ExpressionAscendingMap) {
        $QueryBuilder->Append(' ORDER BY ');
        foreach($QueryBuilder->Iterate($ExpressionAscendingMap, ', ') as $Expression) {
            $Ascending = $ExpressionAscendingMap[$Expression];
            $Direction = $Ascending ? 'ASC' : 'DESC';
            
            $QueryBuilder->AppendExpression($Expression);
            $QueryBuilder->Append(' ' . $Direction);
        }
    }

    protected function AppendRange(QueryBuilder $QueryBuilder, $Offset, $Limit) {
        $QueryBuilder->Append(' ');
        if($Limit === null) {
            /*
             * Mysql cannot have an 'OFFSET' clause without a 'LIMIT' clause
             * 18446744073709551615 is equal to 2^64-1 which is the highest possible value
             * for the 'LIMIT' clause
             */
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