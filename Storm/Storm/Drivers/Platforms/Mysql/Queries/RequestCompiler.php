<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

class RequestCompiler  extends Queries\RequestCompiler {    
    protected function AppendProcedureStatement(QueryBuilder $QueryBuilder, Relational\Procedure $Procedure) {
        $QueryBuilder->AppendIdentifiers('UPDATE # SET ', array_map(
                function ($Table) { 
                    return $Table->GetName();
                }, 
                $Procedure->GetTables()), ',');
    }
    
    protected function AppendProcedureExpressions(QueryBuilder $QueryBuilder, array $Expressions) {
        $First = true;
        foreach($Expressions as $Expression) {
            if($First) $First = false;
            else
                $QueryBuilder->Append(', ');
            
            $this->AppendProcedureExpression($QueryBuilder, $Expression);
        }
    }
    
    protected function AppendPredicates(QueryBuilder $QueryBuilder, array $Predicates) {
        $QueryBuilder->Append(' WHERE TRUE AND ');
        $QueryBuilder->Append('(');
        $First = true;
        foreach($Predicates as $Predicate) {
            if($First) $First = false;
            else
                $QueryBuilder->Append (' AND ');
            
            $QueryBuilder->AppendPredicate($Predicate);
        }
        $QueryBuilder->Append(')');
    }

    protected function AppendOrderedExpressions(QueryBuilder $QueryBuilder, \SplObjectStorage $ExpressionAscendingMap) {
        $QueryBuilder->Append(' ORDER BY ');
        foreach($QueryBuilder->Delimit($ExpressionAscendingMap, ', ') as $Expression) {
            $Ascending = $ExpressionAscendingMap[$Expression];
            $Direction = $Ascending ? 'ASC' : 'DESC';
            
            $QueryBuilder->AppendExpression($Expression);
            $QueryBuilder->Append(' ' . $Direction);
        }
    }

    protected function AppendRange(QueryBuilder $QueryBuilder, $Offset, $Limit) {
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