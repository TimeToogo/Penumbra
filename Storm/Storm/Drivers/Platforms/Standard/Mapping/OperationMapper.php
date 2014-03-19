<?php

namespace Storm\Drivers\Platforms\Standard\Mapping;

use \Storm\Drivers\Platforms\Base\Mapping;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

abstract class OperationMapper extends Mapping\OperationMapper {

    final protected function MatchingBinaryOperators() {
        $MatchingOperators = [
            O\Operators\Binary::Addition => R\Operators\Binary::Addition,
            O\Operators\Binary::BitwiseAnd => R\Operators\Binary::BitwiseAnd,
            O\Operators\Binary::BitwiseOr => R\Operators\Binary::BitwiseOr,
            O\Operators\Binary::BitwiseXor => R\Operators\Binary::BitwiseXor,
            O\Operators\Binary::Concatenation => R\Operators\Binary::Concatenation,
            O\Operators\Binary::Division => R\Operators\Binary::Division,
            O\Operators\Binary::Equality => R\Operators\Binary::NullSafeEquality,
            O\Operators\Binary::Identity => R\Operators\Binary::NullSafeEquality,
            O\Operators\Binary::Inequality => R\Operators\Binary::NullSafeInequality,
            O\Operators\Binary::NotIdentical => R\Operators\Binary::NullSafeInequality,
            O\Operators\Binary::LessThan => R\Operators\Binary::LessThan,
            O\Operators\Binary::LessThanOrEqualTo => R\Operators\Binary::LessThanOrEqualTo,
            O\Operators\Binary::GreaterThan => R\Operators\Binary::GreaterThan,
            O\Operators\Binary::GreaterThanOrEqualTo => R\Operators\Binary::GreaterThanOrEqualTo,
            O\Operators\Binary::LogicalAnd => R\Operators\Binary::LogicalAnd,
            O\Operators\Binary::LogicalOr => R\Operators\Binary::LogicalOr,
            O\Operators\Binary::Modulus => R\Operators\Binary::Modulus,
            O\Operators\Binary::Multiplication => R\Operators\Binary::Multiplication,
            O\Operators\Binary::ShiftLeft => R\Operators\Binary::ShiftLeft,
            O\Operators\Binary::ShiftRight => R\Operators\Binary::ShiftRight,
            O\Operators\Binary::Subtraction => R\Operators\Binary::Subtraction,
        ];
        
        foreach($this->IncompatibleBinaryOperators() as $Operator) {
            unset($MatchingOperators[$Operator]);
        }
        
        return $MatchingOperators;
    }
    
    protected function IncompatibleBinaryOperators() {
        return [];
    }

    final protected function MatchingUnaryOperators() {
        $MatchingOperators = [
            O\Operators\Unary::BitwiseNot => R\Operators\Unary::BitwiseNot,
            O\Operators\Unary::Negation => R\Operators\Unary::Negation,
            O\Operators\Unary::Not => R\Operators\Unary::Not,
        ];
        
        foreach($this->IncompatibleUnaryOperators() as $Operator) {
            unset($MatchingOperators[$Operator]);
        }
        
        return $MatchingOperators;
    }
    
    protected function IncompatibleUnaryOperators() {
        return [];
    }
    
    protected function CastAsTypeExpression($Type, R\Expression $ValueExpression) {
        return R\Expression::FunctionCall('CAST', [
                R\Expression::Multiple([$ValueExpression, R\Expression::Keyword('AS'), R\Expression::Literal($Type)])
        ]);
    }

}

?>