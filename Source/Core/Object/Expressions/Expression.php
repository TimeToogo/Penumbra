<?php

namespace Penumbra\Core\Object\Expressions;

use Penumbra\Core\Object\IProperty;

/**
 * The base class for object expressions.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Expression {
    use \Penumbra\Core\Helpers\Type;
    
    /**
     * @return Expression
     */
    public abstract function Traverse(ExpressionWalker $Walker);
    
    /**
     * @return Expression
     */
    public abstract function Simplify();
    
    /**
     * @return Expression[]
     */
    final protected static function SimplifyAll(array $Expressions) {
        $ReducedExpressions = [];
        foreach($Expressions as $Key => $Expression) {
            $ReducedExpressions[$Key] = $Expression === null ? null : $Expression->Simplify();
        }
        
        return $ReducedExpressions;
    }
    
    /**
     * @return boolean
     */
    final protected static function AllOfType(array $Expressions, $Type) {
        foreach ($Expressions as $Expression) {
            if(!($Expression instanceof $Type)) {
                return false;
            }
        }
        
        return true;
    }


    // <editor-fold desc="Factory Methods">
    
    /**
     * @return AssignmentExpression
     */
    final public static function Assign(
            Expression $AssignToValueExpression, 
            $AssignmentOperator,
            Expression $AssignmentValueExpression) {
        return new AssignmentExpression($AssignToValueExpression, $AssignmentOperator, $AssignmentValueExpression);
    }
    
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
     * @return NewExpression
     */
    final public static function Constructor(Expression $ClassTypeExpression, array $ArgumentValueExpressions = []) {
        return new NewExpression($ClassTypeExpression, $ArgumentValueExpressions);
    }
    
    /**
     * @return MethodCallExpression
     */
    final public static function MethodCall(Expression $ValueExpression, Expression $NameExpression, array $ArgumentValueExpressions = []) {
        return new MethodCallExpression($ValueExpression, $NameExpression, $ArgumentValueExpressions);
    }
    
    /**
     * @return FieldExpression
     */
    final public static function Field(Expression $ValueExpression, Expression $NameExpression) {
        return new FieldExpression($ValueExpression, $NameExpression);
    }
    
    /**
     * @return IndexExpression
     */
    final public static function Index(Expression $ValueExpression, Expression $IndexExpression) {
        return new IndexExpression($ValueExpression, $IndexExpression);
    }
    
    /**
     * @return InvocationExpression
     */
    final public static function Invocation(Expression $ValueExpression, array $ArgumentExpressions) {
        return new InvocationExpression($ValueExpression, $ArgumentExpressions);
    }
    
    /**
     * @return CastExpression
     */
    final public static function Cast($CastType, Expression $CastValueExpression) {
        return new CastExpression($CastType, $CastValueExpression);
    }
    
    /**
     * @return EmptyExpression
     */
    final public static function IsEmpty(Expression $ValueExpression) {
        return new EmptyExpression($ValueExpression);
    }
    
    /**
     * @return FunctionCallExpression
     */
    final public static function FunctionCall(Expression $NameExpression, array $ArgumentValueExpressions = []) {
        return new FunctionCallExpression($NameExpression, $ArgumentValueExpressions);
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
     * @return ReturnExpression
     */
    final public static function ReturnExpression(Expression $ValueExpression = null) {
        return new ReturnExpression($ValueExpression);
    }
    
    /**
     * @return PropertyExpression
     */
    final public static function Property(IProperty $Property, PropertyExpression $ParentPropertyExpression = null) {
        return new PropertyExpression($Property, $ParentPropertyExpression);
    }
    
    /**
     * @return ValueExpression
     */
    final public static function Value($Value) {
        return new ValueExpression($Value);
    }
    
    /**
     * @return UnresolvedVariableExpression
     */
    final public static function UnresolvedVariable(Expression $NameExpression) {
        return new UnresolvedVariableExpression($NameExpression);
    }
    
    /**
     * @return ArrayExpression
     */
    final public static function NewArray(array $KeyExpressions, array $ValueExpressions) {
        return new ArrayExpression($KeyExpressions, $ValueExpressions);
    }
    
    /**
     * @return ClosureExpression
     */
    final public static function Closure(array $ParameterNames, array $UsedVariables, array $BodyExpressions) {
        return new ClosureExpression($ParameterNames, $UsedVariables, $BodyExpressions);
    }
    
    /**
     * @return SubRequestExpression
     */
    final public static function SubRequest(\Penumbra\Core\Object\IRequest $Request) {
        return new SubRequestExpression($Request);
    }
    
    // </editor-fold>
}

?>