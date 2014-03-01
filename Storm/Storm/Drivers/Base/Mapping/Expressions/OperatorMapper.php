<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Object\Expressions\Operators as ObjectOperators;
use \Storm\Drivers\Base\Relational\Expressions\Operators as RelationalOperators;

class OperatorMapper {
    // <editor-fold defaultstate="collapsed" desc="Assignment">
    
    private static $AssignmentOperatorMap = [
        ObjectOperators\Assignment::Addition => RelationalOperators\Assignment::Addition,
        ObjectOperators\Assignment::BitwiseAnd => RelationalOperators\Assignment::BitwiseAnd,
        ObjectOperators\Assignment::BitwiseOr => RelationalOperators\Assignment::BitwiseOr,
        ObjectOperators\Assignment::BitwiseXor => RelationalOperators\Assignment::BitwiseXor,
        ObjectOperators\Assignment::Concatenate => RelationalOperators\Assignment::Concatenate,
        ObjectOperators\Assignment::Division => RelationalOperators\Assignment::Division,
        ObjectOperators\Assignment::Equal => RelationalOperators\Assignment::Equal,
        ObjectOperators\Assignment::EqualReference => RelationalOperators\Assignment::Equal,
        ObjectOperators\Assignment::Modulus => RelationalOperators\Assignment::Modulus,
        ObjectOperators\Assignment::Multiplication => RelationalOperators\Assignment::Multiplication,
        ObjectOperators\Assignment::ShiftLeft => RelationalOperators\Assignment::ShiftLeft,
        ObjectOperators\Assignment::ShiftRight => RelationalOperators\Assignment::ShiftRight,
        ObjectOperators\Assignment::Subtraction => RelationalOperators\Assignment::Subtraction,
    ];


    public function MapAssignmentOperator($AssignmentOperator) {
        return isset(self::$AssignmentOperatorMap[$AssignmentOperator]) ?
                self::$AssignmentOperatorMap[$AssignmentOperator] : null;
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Binary">
    
    private static $BinaryOperatorMap = [
        ObjectOperators\Binary::Addition => RelationalOperators\Binary::Addition,
        ObjectOperators\Binary::BitwiseAnd => RelationalOperators\Binary::BitwiseAnd,
        ObjectOperators\Binary::BitwiseOr => RelationalOperators\Binary::BitwiseOr,
        ObjectOperators\Binary::BitwiseXor => RelationalOperators\Binary::BitwiseXor,
        ObjectOperators\Binary::Concatenation => RelationalOperators\Binary::Concatenation,
        ObjectOperators\Binary::Division => RelationalOperators\Binary::Division,
        ObjectOperators\Binary::Equality => RelationalOperators\Binary::NullSafeEquality,
        ObjectOperators\Binary::Identity => RelationalOperators\Binary::NullSafeEquality,
        ObjectOperators\Binary::Inequality => RelationalOperators\Binary::NullSafeInequality,
        ObjectOperators\Binary::NonIdentity => RelationalOperators\Binary::NullSafeInequality,
        ObjectOperators\Binary::LessThan => RelationalOperators\Binary::LessThan,
        ObjectOperators\Binary::LessThanOrEqualTo => RelationalOperators\Binary::LessThanOrEqualTo,
        ObjectOperators\Binary::GreaterThan => RelationalOperators\Binary::GreaterThan,
        ObjectOperators\Binary::GreaterThanOrEqualTo => RelationalOperators\Binary::GreaterThanOrEqualTo,
        ObjectOperators\Binary::LogicalAnd => RelationalOperators\Binary::LogicalAnd,
        ObjectOperators\Binary::LogicalOr => RelationalOperators\Binary::LogicalOr,
        ObjectOperators\Binary::Modulus => RelationalOperators\Binary::Modulus,
        ObjectOperators\Binary::Multiplication => RelationalOperators\Binary::Multiplication,
        ObjectOperators\Binary::ShiftLeft => RelationalOperators\Binary::ShiftLeft,
        ObjectOperators\Binary::ShiftRight => RelationalOperators\Binary::ShiftRight,
        ObjectOperators\Binary::Subtraction => RelationalOperators\Binary::Subtraction,
    ];

    public function MapBinaryOperator($BinaryOperator) {
        return isset(self::$BinaryOperatorMap[$BinaryOperator]) ?
                self::$BinaryOperatorMap[$BinaryOperator] : null;
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Unary">
    
    private static $UnaryOperatorMap = [
        ObjectOperators\Unary::BitwiseNot => RelationalOperators\Unary::BitwiseNot,
        ObjectOperators\Unary::Decrement => RelationalOperators\Unary::Decrement,
        ObjectOperators\Unary::Increment => RelationalOperators\Unary::Increment,
        ObjectOperators\Unary::Negation => RelationalOperators\Unary::Negation,
        ObjectOperators\Unary::Not => RelationalOperators\Unary::Not,
        ObjectOperators\Unary::PreDecrement => RelationalOperators\Unary::PreDecrement,
        ObjectOperators\Unary::PreIncrement => RelationalOperators\Unary::PreIncrement,
    ];

    public function MapUnaryOperator($UnaryOperator) {
        return isset(self::$UnaryOperatorMap[$UnaryOperator]) ?
                self::$UnaryOperatorMap[$UnaryOperator] : null;
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Cast">
    
    private static $CastOperatorMap = [
        ObjectOperators\Cast::Boolean => RelationalOperators\Cast::Boolean,
        ObjectOperators\Cast::Double => RelationalOperators\Cast::Double,
        ObjectOperators\Cast::Integer => RelationalOperators\Cast::Integer,
        ObjectOperators\Cast::String => RelationalOperators\Cast::String,
    ];
    
    public function MapCastOperator($CastOperator) {
        return isset(self::$CastOperatorMap[$CastOperator]) ?
                self::$CastOperatorMap[$CastOperator] : null;
    }
    // </editor-fold>
}

?>