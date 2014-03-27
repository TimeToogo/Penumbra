<?php

namespace Penumbra\Drivers\Platforms\SQLite\Converters;

use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Expressions\Converters;
use \Penumbra\Drivers\Base\Relational\Expressions\Expression;
use \Penumbra\Core\Relational\Expression as CoreExpression;
use \Penumbra\Drivers\Base\Relational\Expressions as E;
use \Penumbra\Core\Relational\Expressions as EE;
use \Penumbra\Drivers\Base\Relational\Expressions\Operators as O;

final class ExpressionConverter extends Converters\ExpressionConverter {
    
    private static $AssignmentBinaryOperatorMap = [
        O\Assignment::Addition => O\Binary::Addition,
        O\Assignment::BitwiseAnd => O\Binary::BitwiseAnd,
        O\Assignment::BitwiseOr => O\Binary::BitwiseOr,
        O\Assignment::BitwiseXor => O\Binary::BitwiseXor,
        O\Assignment::Concatenate => O\Binary::Concatenation,
        O\Assignment::Division => O\Binary::Division,
        O\Assignment::Modulus => O\Binary::Modulus,
        O\Assignment::Multiplication => O\Binary::Multiplication,
        O\Assignment::ShiftLeft => O\Binary::ShiftLeft,
        O\Assignment::ShiftRight => O\Binary::ShiftRight,
        O\Assignment::Subtraction => O\Binary::Subtraction,
    ];
    public function MapAssignmentExpression(
            EE\ColumnExpression $ColumnExpression, 
            $AssignmentOperator, 
            CoreExpression $ValueExpression) {
        
        if(isset(self::$AssignmentBinaryOperatorMap[$AssignmentOperator])) {
            return Expression::Set(
                    $ColumnExpression, 
                    O\Assignment::Equal, 
                    $this->MapBinaryOperationExpression(
                            $ColumnExpression, 
                            self::$AssignmentBinaryOperatorMap[$AssignmentOperator], 
                            $ValueExpression));
        }
        else {
            return Expression::Set(
                    $ColumnExpression, 
                    O\Assignment::Equal, 
                    $ValueExpression);
        }
    }
        
    public function MapUnaryOperationExpression($UnaryOperator, CoreExpression $OperandExpression) {
        switch ($UnaryOperator) {
            case O\Unary::Increment:
                return new E\BinaryOperationExpression(
                        $OperandExpression, 
                        O\Binary::Addition, 
                        new EE\ConstantExpression(1));
                
            case O\Unary::Decrement:
                return new E\BinaryOperationExpression(
                        $OperandExpression, 
                        O\Binary::Subtraction, 
                        new EE\ConstantExpression(1));
            
            case O\Unary::PreIncrement:
                return new E\BinaryOperationExpression(
                        new EE\ConstantExpression(1), 
                        O\Binary::Addition, 
                        $OperandExpression);
            
            case O\Unary::PreDecrement:
                return new E\BinaryOperationExpression(
                        new EE\ConstantExpression(1), 
                        O\Binary::Subtraction, 
                        $OperandExpression);

            default:
                return Expression::UnaryOperation($UnaryOperator, $OperandExpression);
        }
    }
    
    public function MapCastExpression($CastType, CoreExpression $CastValueExpression) {
        switch ($CastType) {
            case O\Cast::Boolean:
                return Expression::Conditional(
                        $CastValueExpression, 
                        Expression::BoundValue(1), 
                        Expression::BoundValue(0));
            
            default:
                return Expression::Cast($CastType, $CastValueExpression);
        }
    }
    
}

?>