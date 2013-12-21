<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational;
use \Storm\Core\Relational\Expressions\Expression;

interface IExpressionMapper {
    
    /**
     * @return Expression
     */
    public function MapConstantExpression($Value);
    
    /**
     * @return Expression
     */
    public function MapAssignmentExpression(
            Relational\Table $Table,
            Relational\IColumn $Column, 
            $AssignmentOperator, 
            Expression $ValueExpression);
    
    /**
     * @return Expression
     */
    public function MapBinaryOperationExpression(
            Expression $LeftOperandExpression, 
            $BinaryOperator, 
            Expression $RightOperandExpression);
    
    /**
     * @return Expression
     */
    public function MapCastExpression($CastType, Expression $CastValueExpression);
    
    /**
     * @return Expression
     */
    public function MapFunctionCallExpression($FunctionName, array $ArgumentValueExpression);
    
    /**
     * @return Expression
     */
    public function MapUnaryOperationExpression($ObjectUnaryOperator, Expression $OperandExpression);
}

?>