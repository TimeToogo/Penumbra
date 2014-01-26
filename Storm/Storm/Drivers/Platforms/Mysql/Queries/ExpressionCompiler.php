<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Drivers\Platforms\Base\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Core\Relational\Expressions as EE;
use \Storm\Drivers\Base\Relational\Expressions as E;
use \Storm\Drivers\Base\Relational\Expressions\Operators;

final class ExpressionCompiler extends Queries\ExpressionCompiler {
    
    protected function BinaryOperators() {
        return [
            Operators\Binary::Addition => '+',
            Operators\Binary::BitwiseAnd => '&',
            Operators\Binary::BitwiseOr => '|',
            Operators\Binary::BitwiseXor => '^',
            Operators\Binary::Division => '/',
            Operators\Binary::Equality => '<=>',
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
}

?>