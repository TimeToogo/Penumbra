<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Columns\Column;

abstract class Expression extends Relational\Expression {
    
    /**
     * @return ColumnExpression
     */
    public static function Column(Relational\IColumn $Column) {
        return new ColumnExpression($Column);
    }


    /**
     * @return ConstantExpression
     */
    public static function Constant($Value) {
        return new ConstantExpression($Value);
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
     * @return Expression
     */
    public static function ReviveColumn(Relational\IColumn $Column) {
        return $Column instanceof Column ?
                $Column->GetDataType()->GetReviveExpression(Expression::Column($Column)) : 
                Expression::Column($Column);
    }
    
    /**
     * @return Expression
     */
    public static function PersistData(Relational\IColumn $Column, parent $ValueExpression) {
        return $Column instanceof Column ?
                $Column->GetDataType()->GetPersistExpression($ValueExpression) : 
                $ValueExpression;
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
     * @return CastExpression
     */
    public static function Cast($CastType, parent $CastValueExpression) {
        return new CastExpression($CastType, $CastValueExpression);
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
    public static function FunctionCall($Name, ValueListExpression $ArgumentValueListExpression = null) {
        $ArgumentValueListExpression = $ArgumentValueListExpression ?: new ValueListExpression([]);
        return new FunctionCallExpression($Name, $ArgumentValueListExpression);
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
     * @return SetExpression
     */
    public static function Set(Expressions\ColumnExpression $AssignToColumnExpression, $AssignmentOperator, parent $AssignmentValueExpression) {
        return new SetExpression($AssignToColumnExpression, $AssignmentOperator, $AssignmentValueExpression);
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