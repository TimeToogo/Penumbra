<?php

namespace Penumbra\Drivers\Base\Relational\Expressions;

use \Penumbra\Core\Relational;

abstract class Expression extends Relational\Expression {
    
    public abstract function Traverse(ExpressionWalker $Walker);
    
    /**
     * @return ColumnExpression
     */
    public static function Column(Relational\IResultSetSource $Source, Relational\IColumn $Column) {
        return new ColumnExpression($Source, $Column);
    }
    
    /**
     * @return BoundValueExpression
     */
    public static function BoundValue($Value) {
        return new BoundValueExpression($Value);
    }
    
    /**
     * @return BoundValueExpression
     */
    public static function EscapedValue($Value) {
        return new EscapedValueExpression($Value);
    }
    
    /**
     * @return IdentifierExpression
     */
    public static function Identifier(array $Segments) {
        return new IdentifierExpression($Segments);
    }
    
    /**
     * @return MultipleExpression
     */
    public static function Multiple(array $Expressions) {
        return new MultipleExpression($Expressions);
    }
    
    /**
     * @return AliasExpression
     */
    public static function Alias(Expression $ValueExpression, $Alias) {
        return new AliasExpression($ValueExpression, $Alias);
    }
    
    /**
     * @return BinaryOperationExpression
     */
    public static function BinaryOperation(parent $LeftOperandExpression, $Operator, parent $RightOperandExpression) {
        return new BinaryOperationExpression($LeftOperandExpression, $Operator, $RightOperandExpression);
    }
    
    /**
     * @return UnaryOperationExpression
     */
    public static function UnaryOperation($UnaryOperator, parent $OperandExpression) {
        return new UnaryOperationExpression($UnaryOperator, $OperandExpression);
    }
    
    /**
     * @return CompoundBooleanExpression
     */
    public static function CompoundBoolean(array $BooleanExpressions, $LogicalOperator = Binary::LogicalAnd) {
        return new CompoundBooleanExpression($BooleanExpressions, $LogicalOperator);
    }
    
    /**
     * @return FunctionCallExpression
     */
    public static function FunctionCall($Name, array $ArgumentExpressions = []) {
        return new FunctionCallExpression($Name, $ArgumentExpressions);
    }
    
    /**
     * @return KeywordExpression
     */
    public static function Keyword($Keyword) {
        return new KeywordExpression($Keyword);
    }
    
    /**
     * @return LiteralExpression
     */
    public static function Literal($String) {
        return new LiteralExpression($String);
    }
    
    /**
     * @return SubSelectExpression
     */
    public static function SubSelect(Relational\Select $Select) {
        return new SubSelectExpression($Select);
    }
    
    /**
     * @return IfExpression
     */
    public static function Conditional(
            parent $ConditionExpression, 
            parent $IfTrueExpression, 
            parent $IfFalseExpression) {
        return new IfExpression($ConditionExpression, $IfTrueExpression, $IfFalseExpression);
    }
    
    /**
     * @return ValueListExpression
     */
    public static function ValueList(array $ValueExpressions = []) {
        return new ValueListExpression($ValueExpressions);
    }
}

?>