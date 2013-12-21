<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expressions;

abstract class Expression extends Expressions\Expression {
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
     * @return FunctionCallExpression
     */
    public static function FunctionCall($Name, ValueListExpression $ArgumentValueListExpression) {
        return new FunctionCallExpression($Name, $ArgumentValueListExpression);
    }
    
    /**
     * @return KeywordExpression
     */
    public static function Keyword($Keyword) {
        return new KeywordExpression($Keyword);
    }
    
    /**
     * @return SetExpression
     */
    public static function Set(Expressions\ColumnExpression $AssignToColumnExpression, $AssignmentOperator, parent $AssignmentValueExpression) {
        return new SetExpression($AssignToColumnExpression, $AssignmentOperator, $AssignmentValueExpression);
    }
    
    
    /**
     * @return ValueListExpression
     */
    public static function ValueList(array $ValueExpressions = array()) {
        return new ValueListExpression($ValueExpressions);
    }
}

?>