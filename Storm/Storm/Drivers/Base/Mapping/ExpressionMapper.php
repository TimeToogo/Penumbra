<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Mapping\IEntityRelationalMap;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Expressions as RR;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Drivers\Base\Relational\Expressions\Converters\IExpressionConverter;
use \Storm\Drivers\Base\Relational\Queries\IExpressionOptimizer;

final class ExpressionMapper {
    /**
     * @var OperatorMapper 
     */
    private $OperatorMapper;
    /**
     * @var IExpressionConverter 
     */
    private $ExpressionConverter;
    /**
     * @var IExpressionOptimizer 
     */
    private $ExpressionOptimizer;
    
    public function __construct(IExpressionConverter $ExpressionConverter, IExpressionOptimizer $ExpressionOptimizer) {
        $this->OperatorMapper = new OperatorMapper();
        $this->ExpressionConverter = $ExpressionConverter;
        $this->ExpressionOptimizer = $ExpressionOptimizer;
    }

    /**
     * @return Relational\Expression[]
     */
    final public function MapAll(IEntityRelationalMap $EntityRelationalMap, array $Expressions) {
        foreach($Expressions as $Key => $Expression) {
            $Expressions[$Key] = $this->Map($EntityRelationalMap, $Expression);
        }
        
        return $Expressions;
    }
    
    /**
     * @return Relational\Expression
     */
    final public function Map(IEntityRelationalMap $EntityRelationalMap, O\Expression $Expression) {
        switch (true) {
             case $Expression instanceof O\PropertyExpression:
                return $this->MapProperty($EntityRelationalMap, $Expression);
            
            case $Expression instanceof O\ConstantExpression:
                return $this->MapConstant($EntityRelationalMap, $Expression);
                        
            case $Expression instanceof O\ObjectExpression:
                return $this->MapObject($EntityRelationalMap, $Expression);
            
            case $Expression instanceof O\PropertyFetchExpression:
                return $this->MapPropertyFetch($EntityRelationalMap, $Expression);
            
            case $Expression instanceof O\MethodCallExpression:
                return $this->MapMethodCall($EntityRelationalMap, $Expression);
            
            case $Expression instanceof O\ArrayExpression:
                return $this->MapArray($EntityRelationalMap, $Expression);
            
            case $Expression instanceof O\AssignmentExpression:
                return $this->MapAssignment($EntityRelationalMap, $Expression);
                        
            case $Expression instanceof O\BinaryOperationExpression:
                return $this->MapBinaryOperation($EntityRelationalMap, $Expression);
            
            case $Expression instanceof O\UnaryOperationExpression:
                return $this->MapUnaryOperation($EntityRelationalMap, $Expression);
            
            case $Expression instanceof O\CastExpression:
                return $this->MapCast($EntityRelationalMap, $Expression);
            
            case $Expression instanceof O\FunctionCallExpression:
                return $this->MapFunctionCall($EntityRelationalMap, $Expression);
            
            case $Expression instanceof O\TernaryExpression:
                return $this->MapTernary($EntityRelationalMap, $Expression);
            
            default:
                throw new Mapping\MappingException(
                        'Unsupported object expression type: %s given',
                        get_class($Expression));
        }
    }
        
    private function MapConstant(IEntityRelationalMap $EntityRelationalMap, O\ConstantExpression $Expression) {
        return $this->ExpressionConverter->MapConstantExpression($Expression->GetValue());
    }
        
    private function MapObject(IEntityRelationalMap $EntityRelationalMap, O\ObjectExpression $Expression) {
        if(!$Expression->HasInstance()) {
            throw new Mapping\MappingException(
                    'Cannot map object expression without a valid object instance.');
        }
        return $this->ExpressionConverter->MapObjectExpression(
                $Expression->GetClassType(), 
                $Expression->GetInstance());
    }
        
    private function MapPropertyFetch(IEntityRelationalMap $EntityRelationalMap, O\PropertyFetchExpression $Expression) {
        $ObjectExpression = $Expression->GetObjectExpression();
        return $this->ExpressionConverter->MapPropertyFetchExpression(
                $Expression->IsStatic() ? 
                        null : $this->MapExpression($EntityRelationalMap, $ObjectExpression),
                $Expression->GetClassType(),
                $Expression->GetName());
    }
        
    private function MapMethodCall(IEntityRelationalMap $EntityRelationalMap, O\MethodCallExpression $Expression) {
        $ObjectExpression = $Expression->GetObjectExpression();
        return $this->ExpressionConverter->MapMethodCallExpression(
                $Expression->IsStatic() ? 
                        null : $this->MapExpression($EntityRelationalMap, $ObjectExpression)[0],
                $Expression->GetClassType(),
                $Expression->GetName(),
                array_map(function($Expression) use (&$EntityRelationalMap) {
                    return $this->MapExpression($EntityRelationalMap, $Expression)[0];
                }, $Expression->GetArgumentValueExpressions()));
    }
        
    private function MapArray(IEntityRelationalMap $EntityRelationalMap, O\ArrayExpression $Expression) {
        return Expression::ValueList(array_map(
                function($Expression) use (&$EntityRelationalMap) {
                    return $this->MapExpression($EntityRelationalMap, $Expression)[0];
                }, $Expression->GetValueExpressions()));
    }
        
    private function MapAssignment(IEntityRelationalMap $EntityRelationalMap, O\AssignmentExpression $Expression) {
        $ColumnExpressions = array_map([Expression::GetType(), 'Column'], 
                        $EntityRelationalMap->GetMappedPersistColumns($Expression->GetProperty()));
        $Operator = $this->OperatorMapper->MapAssignmentOperator($Expression->GetOperator());
        $SetValueExpression = $this->MapExpression($EntityRelationalMap, $Expression->GetRightOperandExpression()),

        return array_map(
                function ($ColumnExpression) use (&$ExpressionConverter, &$Operator, &$SetValueExpression) {
                    return $ExpressionMapper->MapAssignmentExpression(
                            $ColumnExpression, 
                            $Operator, 
                            $SetValueExpression);
                }, $ColumnExpressions); 
    }
        
    private function MapBinaryOperation(IEntityRelationalMap $EntityRelationalMap, O\BinaryOperationExpression $Expression) {
        return $ExpressionConverter->MapBinaryOperationExpression(
                $this->MapExpression($EntityRelationalMap, $Expression->GetLeftOperandExpression()), 
                $this->OperatorMapper->MapBinaryOperator($Expression->GetOperator()), 
                $this->MapExpression($EntityRelationalMap, $Expression->GetRightOperandExpression())
                );
    }
        
    private function MapUnaryOperation(IEntityRelationalMap $EntityRelationalMap, O\UnaryOperation $Expression) {
        return $ExpressionConverter->MapUnaryOperationExpression(
                $this->OperatorMapper->MapUnaryOperator($Expression->GetOperator()), 
                $this->MapExpression($EntityRelationalMap, $Expression->GetOperandExpression())
                );
    }
        
    private function MapCast(IEntityRelationalMap $EntityRelationalMap, O\CastExpression $Expression) {
        return $ExpressionConverter->MapCast(
                $this->OperatorMapper->MapCastOperator($Expression->GetCastType()),
                $this->MapExpression($EntityRelationalMap, $Expression->GetCastValueExpression())
                );
    }
        
    private function MapFunctionCall(IEntityRelationalMap $EntityRelationalMap, O\FunctionCallExpression $Expression) {
        return $ExpressionConverter->MapFunctionCall(
                $Expression->GetName(),
                array_map(function($Expression) use (&$EntityRelationalMap) {
                    return $this->MapExpression($EntityRelationalMap, $Expression);
                }, $Expression->GetArgumentValueExpressions())
                );
    }
        
    private function MapTernary(IEntityRelationalMap $EntityRelationalMap, O\TernaryExpression $Expression) {
        return $ExpressionConverter->MapIfExpression(
                $this->MapExpression($EntityRelationalMap, $Expression->GetConditionExpression()),
                $this->MapExpression($EntityRelationalMap, $Expression->GetIfTrueExpression()),
                $this->MapExpression($EntityRelationalMap, $Expression->GetIfFalseExpression())
                );
    }
        
    private function MapPropertyExpression(IEntityRelationalMap $EntityRelationalMap, O\PropertyExpression $Expression) {
        return array_map(
            [Expression::GetType(), 'ReviveColumn'], 
            $EntityRelationalMap->GetMappedReviveColumns($Expression->GetProperty()));
    }
}

?>