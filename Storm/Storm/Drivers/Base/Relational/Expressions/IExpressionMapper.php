<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;

interface IExpressionMapper {
    
    /**
     * @return CoreExpression
     */
    public function MapConstantExpression($Value);
    
    /**
     * @return CoreExpression
     */
    public function MapObjectExpression($Type, $Value);
        
    /**
     * @return CoreExpression
     */
    public function MapMethodCallExpression(CoreExpression $ObjectExpression = null, $Type, $Name, array $ArgumentValueExpressions);
    
        
    /**
     * @return CoreExpression
     */
    public function MapPropertyFetchExpression(CoreExpression $ObjectExpression = null, $Type, $Name);
    
    /**
     * @return CoreExpression
     */
    public function MapAssignmentExpression(
            Relational\Expressions\ColumnExpression $Column, 
            $AssignmentOperator, 
            CoreExpression $ValueExpression);
    
    /**
     * @return CoreExpression
     */
    public function MapBinaryOperationExpression(
            CoreExpression $LeftOperandExpression, 
            $BinaryOperator, 
            CoreExpression $RightOperandExpression);
    
    /**
     * @return CoreExpression
     */
    public function MapCastExpression($CastType, CoreExpression $CastValueExpression);
    
    /**
     * @return CoreExpression
     */
    public function MapFunctionCallExpression($FunctionName, array $ArgumentValueExpression);
    
    /**
     * @return CoreExpression
     */
    public function MapIfExpression(
            CoreExpression $ConditionExpression,
            CoreExpression $IfTrueExpression, 
            CoreExpression $IfFalseExpression);
    
    /**
     * @return CoreExpression
     */
    public function MapUnaryOperationExpression($UnaryOperator, CoreExpression $OperandExpression);
}

?>