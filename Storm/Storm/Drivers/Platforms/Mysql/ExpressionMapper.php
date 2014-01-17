<?php

namespace Storm\Drivers\Platforms\Mysql;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions as E;
use \Storm\Core\Relational\Expressions as EE;
use \Storm\Drivers\Base\Relational\Expressions\Operators as O;

final class ExpressionMapper extends E\ExpressionMapper {
    
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
    
    public function MapBinaryOperationExpression(
            CoreExpression $LeftOperandExpression, 
            $BinaryOperator, 
            CoreExpression $RightOperandExpression) {
        
        if($LeftOperandExpression instanceof E\BinaryOperationExpression) {
            $NestedOperand = $LeftOperandExpression->GetLeftOperandExpression();
            if($NestedOperand instanceof E\FunctionCallExpression
                    && $NestedOperand->GetName() === 'LOCATE') {
                if($RightOperandExpression instanceof EE\ConstantExpression
                        && $RightOperandExpression->GetValue() === false) {
                    $RightOperandExpression = Expression::Constant(-1);
                }
            }
        }
        
        switch($BinaryOperator) {
            case O\Binary::Concatenation:
                return new E\FunctionCallExpression('CONCAT', Expression::ValueList([$LeftOperandExpression, $RightOperandExpression]));
                
            case O\Binary::Inequality:
                return Expression::UnaryOperation(O\Unary::Not, 
                        Expression::BinaryOperation(
                                $LeftOperandExpression, 
                                O\Binary::Equality, 
                                $RightOperandExpression));
            
            default:
                return new E\BinaryOperationExpression(
                        $LeftOperandExpression, 
                        $BinaryOperator, 
                        $RightOperandExpression);
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
                        Expression::Constant(1), 
                        Expression::Constant(0));
            
            case O\Cast::Double:
                return Expression::BinaryOperation(
                        $CastValueExpression, 
                        O\Binary::Addition, 
                        '0.0D');
                
            case O\Cast::Integer:
                return Expression::Cast('INTEGER', $CastValueExpression);
            
            case O\Cast::String:
                return Expression::Cast('CHAR', $CastValueExpression);
            
            default:
                throw new \Exception();
        }
    }
    
}

?>