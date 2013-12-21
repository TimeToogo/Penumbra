<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

class RequestCompiler  extends Queries\RequestCompiler {
    public function __construct(
            Queries\IExpressionCompiler $ExpressionCompiler, 
            Queries\IPredicateCompiler $PredicateCompiler) {
        parent::__construct($ExpressionCompiler, $PredicateCompiler);
    }
    
    protected function AppendOperationStatement(QueryBuilder $QueryBuilder, Relational\Operation $Operation) {
        $QueryBuilder->AppendIdentifier('UPDATE # ', [$Operation->GetTables()->GetName()]);
    }
    
    protected function AppendOperationExpressions(QueryBuilder $QueryBuilder, array $Expressions) {
        $First = true;
        foreach($Expressions as $Expression) {
            if($First) $First = false;
            else
                $QueryBuilder->Append(', ');
            
            $this->AppendOperationExpression($QueryBuilder, $Expression);
        }
    }
    
    protected function AppendPredicates(QueryBuilder $QueryBuilder, Relational\Table $Table, array $Predicates) {
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

    protected function AppendOrderedColumns(QueryBuilder $QueryBuilder, Relational\Table $Table, \SplObjectStorage $ColumnAscendingMap) {
        $QueryBuilder->Append(' ORDER BY ');
        $First = true;
        foreach($ColumnAscendingMap as $Column) {
            if($First) $First = false;
            else
                $QueryBuilder->Append(', ');

            $Ascending = $ColumnAscendingMap[$Column];
            $Direction = $Ascending ? 'ASC' : 'DESC';
            $QueryBuilder->AppendIdentifier('# ' . $Direction, [$Table->GetName(), $Column->GetName()]);
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