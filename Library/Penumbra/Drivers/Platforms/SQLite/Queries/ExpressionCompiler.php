<?php

namespace Penumbra\Drivers\Platforms\SQLite\Queries;

use \Penumbra\Drivers\Platforms\Base\Queries;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;
use \Penumbra\Core\Relational\Expressions as EE;
use \Penumbra\Drivers\Base\Relational\Expressions as E;
use \Penumbra\Drivers\Base\Relational\Expressions\Operators;

final class ExpressionCompiler extends Queries\ExpressionCompiler {
    
    protected function BinaryOperators() {
        return [
            Operators\Binary::Concatenation => '||',
            Operators\Binary::Addition => '+',
            Operators\Binary::BitwiseAnd => '&',
            Operators\Binary::BitwiseOr => '|',
            Operators\Binary::BitwiseXor => '^',
            Operators\Binary::Division => '/',
            Operators\Binary::Equality => '=',
            Operators\Binary::Inequality => '!=',
            Operators\Binary::NullSafeEquality => 'IS',
            Operators\Binary::NullSafeInequality => 'IS NOT',
            Operators\Binary::GreaterThan => '>',
            Operators\Binary::GreaterThanOrEqualTo => '>=',
            Operators\Binary::In => 'IN',
            Operators\Binary::LessThan => '<',
            Operators\Binary::LessThanOrEqualTo => '<=',
            Operators\Binary::LogicalAnd => 'AND',
            Operators\Binary::LogicalOr => 'OR',
            Operators\Binary::MatchesRegularExpression => 'REGEXP',
            Operators\Binary::Modulus => '%',
            Operators\Binary::Multiplication => '*',
            Operators\Binary::ShiftLeft => '<<',
            Operators\Binary::ShiftRight => '>>',
            Operators\Binary::Subtraction => '-',
        ];
    }

    protected function SetOperators() {
        return [
            Operators\Assignment::Equal => '=',
        ];
    }

    protected function UnaryOperators() {
        return [
            Operators\Unary::BitwiseNot => '~',
            Operators\Unary::Negation => '-',
            Operators\Unary::Not => '!',
        ];
    }
    
    protected function AppendCast(QueryBuilder $QueryBuilder, E\CastExpression $Expression) {
        parent::AppendCast($QueryBuilder, $Expression);
    }
    
    protected function AppendIf(QueryBuilder $QueryBuilder, E\IfExpression $Expression) {
        $QueryBuilder->Append('CASE WHEN ');
        $this->Append($QueryBuilder, $Expression->GetConditionExpression());
        $QueryBuilder->Append(' THEN ');
        $this->Append($QueryBuilder, $Expression->GetIfTrueExpression());
        $QueryBuilder->Append(' ELSE ');
        $this->Append($QueryBuilder, $Expression->GetIfFalseExpression());
        $QueryBuilder->Append(' END ');
    }

    protected function CastTypes() {
       return [
            Operators\Cast::Double => 'REAL',
            Operators\Cast::Integer => 'INTEGER',
            Operators\Cast::String => 'TEXT',
        ]; 
    }
    
    protected function GetCastAsKeyword() {
        return 'TO';
    }
}

?>