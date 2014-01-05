<?php

namespace Storm\Core\Object\Expressions;

use Storm\Core\Object\IProperty;

abstract class Expression {
    use \Storm\Core\Helpers\Type;
    
    // <editor-fold defaultstate="collapsed" desc="Factory Methods">    
    /**
     * @return BinaryOperationExpression
     */
    final public static function BinaryOperation(Expression $LeftOperandExpression, $Operator, Expression $RightOperandExpression) {
        return new BinaryOperationExpression($LeftOperandExpression, $Operator, $RightOperandExpression);
    }
    
    /**
     * @return CastExpression
     */
    final public static function Cast($CastType, Expression $CastValueExpression) {
        return new CastExpression($CastType, $CastValueExpression);
    }
    
    /**
     * @return FunctionCallExpression
     */
    final public static function FunctionCall($Name, array $ArgumentValueExpressions = array()) {
        return new FunctionCallExpression($Name, $ArgumentValueExpressions);
    }
    
    /**
     * @return PropertyExpression
     */
    final public static function Property(IProperty $Property) {
        return new PropertyExpression($Property);
    }
    
    
    /**
     * @return ConstantExpression
     */
    final public static function Constant($Value) {
        return new ConstantExpression($Value);
    }

    /**
     * @return AssignmentExpression
     */
    final public static function Assign(Expression $AssignToValueExpression, Expression $AssignmentValueExpression,
            $AssignmentOperator = Operators\Assignment::Equal) {
        return new AssignmentExpression($AssignToValueExpression, $AssignmentOperator, $AssignmentValueExpression);
    }
    // </editor-fold>
}

?>