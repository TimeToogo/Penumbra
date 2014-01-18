<?php

namespace Storm\Core\Object\Expressions;

use Storm\Core\Object\IProperty;

/**
 * The base class for object expressions.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
     * @return UnaryOperationExpression
     */
    final public static function UnaryOperation($UnaryOperator, Expression $OperandExpression) {
        return new UnaryOperationExpression($UnaryOperator, $OperandExpression);
    }
    
    /**
     * @return ObjectExpression
     */
    final public static function Object($InstanceOrType) {
        return new ObjectExpression($InstanceOrType);
    }
    
    /**
     * @return NewExpression
     */
    final public static function Construct($ClassType, array $ArgumentValueExpressions = array()) {
        return new NewExpression($ClassType, $ArgumentValueExpressions);
    }
    
    /**
     * @return MethodCallExpression
     */
    final public static function MethodCall(Expression $ObjectExpression, $Name, array $ArgumentValueExpressions = array()) {
        return new MethodCallExpression($ObjectExpression, $Name, $ArgumentValueExpressions);
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
     * @return TernaryExpression
     */
    final public static function Ternary(
            Expression $ConditionExpression,
            Expression $IfTrueExpression, 
            Expression $IfFalseExpression) {
        return new TernaryExpression($ConditionExpression, $IfTrueExpression, $IfFalseExpression);
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
     * @return ArrayExpression
     */
    final public static function NewArray(array $ValueExpressions) {
        return new ArrayExpression($ValueExpressions);
    }

    /**
     * @return AssignmentExpression
     */
    final public static function Assign(
            Expression $AssignToValueExpression, 
            $AssignmentOperator,
            Expression $AssignmentValueExpression) {
        return new AssignmentExpression($AssignToValueExpression, $AssignmentOperator, $AssignmentValueExpression);
    }
    // </editor-fold>
}

?>