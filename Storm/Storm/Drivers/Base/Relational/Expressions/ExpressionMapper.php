<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Core\Relational\Expressions\ColumnExpression;

abstract class ExpressionMapper implements IExpressionMapper {
    private $FunctionMapper;
    private $ObjectMapper;
    
    public function __construct(IFunctionMapper $FunctionMapper, IObjectMapper $ObjectMapper) {
        $this->FunctionMapper = $FunctionMapper;
        $this->ObjectMapper = $ObjectMapper;
    }
    
    public function MapConstantExpression($Value) {
        return Expression::Constant($Value);
    }
    
    public function MapAssignmentExpression(
            ColumnExpression $ColumnExpression, 
            $AssignmentOperator, 
            CoreExpression $ValueExpression) {
        
        return Expression::Set(
                $ColumnExpression, 
                $AssignmentOperator, 
                $ValueExpression);
    }
    
    public function MapBinaryOperationExpression(
            CoreExpression $LeftOperandExpression, 
            $BinaryOperator, 
            CoreExpression $RightOperandExpression) {
        
        return Expression::BinaryOperation(
                $LeftOperandExpression, 
                $BinaryOperator, 
                $RightOperandExpression);
    }
    
    public function MapUnaryOperationExpression(
            $UnaryOperator, 
            CoreExpression $OperandExpression) {
        
        return Expression::UnaryOperation(
                $UnaryOperator, 
                $OperandExpression);
    }
    
    public function MapCastExpression($CastType, CoreExpression $CastValueExpression) {
        return Expression::Cast(
                $CastType, 
                $CastValueExpression);
    }
    
    public function MapIfExpression(
            CoreExpression $ConditionExpression, 
            CoreExpression $IfTrueExpression, 
            CoreExpression $IfFalseExpression) {
        
        return Expression::Conditional(
                $ConditionExpression, 
                $IfTrueExpression, 
                $IfFalseExpression);
    }
    
    final public function MapObjectExpression($Type, $Value) {
        return $this->ObjectMapper->MapObjectExpression($Type, $Value);
    }
    
    final public function MapMethodCallExpression(
            CoreExpression $ObjectValueExpression = null, 
            $Type, 
            $Name, 
            array $ArgumentValueExpressions) {
        
        return $this->ObjectMapper->MapMethodCallExpression(
                $ObjectValueExpression, 
                $Type, 
                $Name, 
                $ArgumentValueExpressions);
    }
    
    final public function MapFunctionCallExpression(
            $FunctionName, 
            array $ArgumentValueExpression) {
        
        return $this->FunctionMapper->MapFunctionCallExpression(
                $FunctionName, 
                $ArgumentValueExpression);
    }
}

?>