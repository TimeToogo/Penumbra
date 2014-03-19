<?php

namespace Storm\Drivers\Platforms\Mysql\Mapping;

use \Storm\Drivers\Platforms\Standard\Mapping;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

class OperationMapper extends Mapping\OperationMapper {
    
    protected function IncompatibleBinaryOperators() {
        return [
            O\Operators\Binary::Inequality,
            O\Operators\Binary::NotIdentical,
            O\Operators\Binary::Concatenation,
        ];
    }
    
    protected function MapBinaryOperation(R\Expression $LeftOperand, $Operator, R\Expression $RightOperand) {
        switch ($Operator) {
            case O\Operators\Binary::Inequality:
            case O\Operators\Binary::NotIdentical:
                return R\Expression::UnaryOperation(
                        R\Operators\Unary::Not,
                        R\Expression::BinaryOperation(
                                $LeftOperand, 
                                R\Operators\Binary::NullSafeEquality, 
                                $RightOperand));
                
            case O\Operators\Binary::Concatenation:
                return R\Expression::FunctionCall('CONCAT', [$LeftOperand, $RightOperand]);
                
            default:
                return;
        }
    }

    protected function MapUnaryOperation($Operator, R\Expression $Operand) {
        
    }

    protected function MatchingCastTypes() {
        return [
            O\Operators\Cast::Integer => 'SIGNED INTEGER',
            O\Operators\Cast::String => 'CHAR',
        ];
    }

    protected function MapCastOperation($CastType, R\Expression $ValueExpression) {
        switch ($CastType) {
            case O\Cast::Boolean:
                return R\Expression::BinaryOperation(
                        $ValueExpression, 
                        R\Operators\Binary::LogicalAnd,
                        R\Expression::BoundValue(1));
            
            case O\Cast::Double:
                return R\Expression::BinaryOperation(
                        $ValueExpression, 
                        O\Binary::Addition, 
                        R\Expression::Literal('0.0'));
             
            default:
                return;
        }
    }
}

?>