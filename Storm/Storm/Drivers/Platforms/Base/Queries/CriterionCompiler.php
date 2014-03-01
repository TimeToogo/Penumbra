<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

abstract class CriterionCompiler extends Queries\CriterionCompiler {
    public function __construct() {
        $this->JoinTypes = $this->JoinTypes();
    }
    
    protected function AppendTable(QueryBuilder $QueryBuilder, Relational\ITable $Table) {
        $QueryBuilder->AppendIdentifier(' FROM #', [$Table->GetName()]);
    }
    
    protected function AppendJoins(QueryBuilder $QueryBuilder, array $Joins) {
        foreach($QueryBuilder->Delimit($Joins, ' ') as $Join) {
            $QueryBuilder->Append($this->GetJoinType($Join->GetJoinType()) . ' ');
            $QueryBuilder->AppendIdentifier('#', [$Join->GetTable()->GetName()]);
            $QueryBuilder->Append(' ON ');
            $QueryBuilder->AppendExpression($Join->GetJoinPredicateExpression());
        }
    }
    
    private $JoinTypes;
    protected function JoinTypes() {
        return [
            Relational\JoinType::Inner => 'INNER JOIN',
            Relational\JoinType::Left => 'LEFT JOIN',
            Relational\JoinType::Right => 'RIGHT JOIN',
            Relational\JoinType::Full => 'FULL JOIN',
            Relational\JoinType::Cross => 'CROSS JOIN',
        ];
    }
    private function GetJoinType($JoinType) {
        if (isset($this->JoinTypes[$JoinType])) {
            return ' ' . $this->JoinTypes[$JoinType] . ' ';
        }
        else {
            throw new \Storm\Drivers\Base\Relational\PlatformException(
                    '%s does not support the supplied join type: %s', 
                    get_class($this),
                    $JoinType);
        }
    }
    
    protected function AppendPredicateExpressions(QueryBuilder $QueryBuilder, array $PredicateExpressions) {
        $QueryBuilder->Append(' WHERE (');
        foreach($QueryBuilder->Delimit($PredicateExpressions, ' AND ') as $PredicateExpression) {
            $QueryBuilder->AppendExpression($PredicateExpression);
        }
        $QueryBuilder->Append(')');
    }
    
    protected function AppendGroupByExpressions(QueryBuilder $QueryBuilder, array $Expressions) {
        $QueryBuilder->Append(' GROUP BY ');
        foreach($QueryBuilder->Delimit($Expressions, ', ') as $Expression) {            
            $QueryBuilder->AppendExpression($Expression);
        }
    }
    
    protected function AppendAggregatePredicateExpressions(QueryBuilder $QueryBuilder, array $Expressions) {
        $QueryBuilder->Append(' HAVING ');
        foreach($QueryBuilder->Delimit($Expressions, ' AND ') as $Expression) {            
            $QueryBuilder->AppendExpression($Expression);
        }
    }

    protected function AppendOrderByExpressions(QueryBuilder $QueryBuilder, \SplObjectStorage $ExpressionAscendingMap) {
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