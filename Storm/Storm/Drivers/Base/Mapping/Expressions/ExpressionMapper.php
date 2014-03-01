<?php

namespace Storm\Drivers\Base\Relational\Expressions\Converters;

use \Storm\Core\Relational\Expression as CoreExpression;
use \Storm\Core\Relational\Expressions\ColumnExpression;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Expressions as RR;

abstract class ExpressionMapper implements IExpressionMapper {
    private $FunctionConverter;
    private $ObjectConverter;
    
    public function __construct(IFunctionConverter $FunctionConverter, IObjectConveter $ObjectConverter) {
        $this->FunctionConverter = $FunctionConverter;
        $this->ObjectConverter = $ObjectConverter;
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
        return $this->ObjectConverter->MapObjectExpression($Type, $Value);
    }
    
    final public function MapPropertyFetchExpression(
            CoreExpression $ObjectExpression = null,
            $Type, 
            $Name) {
        
        return $this->ObjectConverter->MapPropertyFetchExpression(
                $ObjectExpression, 
                $Type, 
                $Name);
    }
    
    final public function MapMethodCallExpression(
            CoreExpression $ObjectValueExpression = null, 
            $Type, 
            $Name, 
            array $ArgumentValueExpressions) {
        
        return $this->ObjectConverter->MapMethodCallExpression(
                $ObjectValueExpression, 
                $Type, 
                $Name, 
                $ArgumentValueExpressions);
    }
    
    final public function MapFunctionCallExpression(
            $FunctionName, 
            array $ArgumentValueExpression) {
        
        return $this->FunctionConverter->MapFunctionCallExpression(
                $FunctionName, 
                $ArgumentValueExpression);
    }
}

?>