<?php

namespace Penumbra\Drivers\Platforms\Mysql\Queries;

use \Penumbra\Drivers\Platforms\Standard\Queries;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;
use \Penumbra\Drivers\Base\Relational\Expressions as R;
use \Penumbra\Drivers\Base\Relational\Expressions\Operators;

final class ExpressionCompilerVisitor extends Queries\ExpressionCompilerVisitor {
    
    protected function BinaryOperators() {
        return [
            Operators\Binary::Addition => '+',
            Operators\Binary::BitwiseAnd => '&',
            Operators\Binary::BitwiseOr => '|',
            Operators\Binary::BitwiseXor => '^',
            Operators\Binary::Division => '/',
            Operators\Binary::Equality => '=',
            Operators\Binary::Inequality => '!=',
            Operators\Binary::NullSafeEquality => '<=>',
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

    protected function UnaryOperators() {
        return [
            Operators\Unary::BitwiseNot => '~',
            Operators\Unary::Negation => '-',
            Operators\Unary::Not => '!',
        ];
    }
    
    protected function CastTypes() {
       return [
            Operators\Cast::Integer => 'INTEGER',
            Operators\Cast::String => 'CHAR',
        ]; 
    }
    
    protected function AppendIf(QueryBuilder $QueryBuilder, R\IfExpression $Expression) {
        $QueryBuilder->Append('IF');
        $QueryBuilder->Append('(');
        $this->Append($QueryBuilder, $Expression->GetConditionExpression());
        $QueryBuilder->Append(',');
        $this->Append($QueryBuilder, $Expression->GetIfTrueExpression());
        $QueryBuilder->Append(',');
        $this->Append($QueryBuilder, $Expression->GetIfFalseExpression());
        $QueryBuilder->Append(')');
    }
}

?>