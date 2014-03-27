<?php

namespace Penumbra\Drivers\Base\Mapping;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object\Expressions as O;
use \Penumbra\Core\Relational;
use \Penumbra\Core\Relational\Expressions as R;

final class ExpressionMapper {
    /**
    * @var PropertyExpressionResolver
    */
    private $PropertyExpressionResolver;
    
    /**
     * @var Expressions\IValueMapper
     */
    private $ValueMapper;
    
    /**
     * @var Expressions\IArrayMapper
     */
    private $ArrayMapper;
    
    /**
     * @var Expressions\IOperationMapper
     */
    private $OperationMapper;
    
    /**
     * @var Expressions\IFunctionMapper
     */
    private $FunctionMapper;
    
    /**
     * @var Expressions\IAggregateMapper
     */
    private $AggregateMapper;
    
    /**
     * @var Expressions\IObjectTypeMapper[]
     */
    private $ObjectTypeMappers;
    
    /**
     * @var Expressions\IControlFlowMapper
     */
    private $ControlFlowMapper;
    
    public function __construct(
            PropertyExpressionResolver $PropertyExpressionResolver,
            Expressions\IValueMapper $ValueMapper, 
            Expressions\IArrayMapper $ArrayMapper, 
            Expressions\IOperationMapper $OperationMapper,
            Expressions\IFunctionMapper $FunctionMapper, 
            Expressions\IAggregateMapper $AgreggateMapper, 
            array $ObjectTypeMappers,  
            Expressions\IControlFlowMapper $ControlFlowMapper) {
        $this->PropertyExpressionResolver = $PropertyExpressionResolver;
        $this->ValueMapper = $ValueMapper;
        $this->ArrayMapper = $ArrayMapper;
        $this->OperationMapper = $OperationMapper;
        $this->FunctionMapper = $FunctionMapper;
        $this->AggregateMapper = $AgreggateMapper;
        $this->ObjectTypeMappers = $ObjectTypeMappers;
        $this->ControlFlowMapper = $ControlFlowMapper;
    }
    
    /**
     * @return Expressions\IObjectTypeMapper
     */
    private function GetObjectTypeMapper($ClassType) {
        while ($ClassType !== false) {
            if(isset($this->ObjectTypeMappers[$ClassType])) {
                return $this->ObjectTypeMappers[$ClassType];
            }
            
            $ClassType = get_parent_class($ClassType);
        }
        
        throw new Mapping\MappingException(
                'Cannot map object of type %s: no suitable object type mapper is defined',
                $ClassType);
    }
    
    final public function MapAll(array $Expressions, array &$ReturnTypes = []) {
        foreach($Expressions as $Key => $Expression) {
            $ReturnType = null;
            $Expressions[$Key] = $this->Map($Expression, $ReturnType);
            $ReturnTypes[$Key] = $ReturnType;
        }
        
        return $Expressions;
    }
    
    final public function Map(O\Expression $Expression = null, &$ReturnType = null) {
        if($Expression === null) {
            return null;
        }
        switch (true) {
            case $Expression instanceof O\ValueExpression:
                return $this->MapValue($Expression, $ReturnType);
           
            case $Expression instanceof O\NewExpression:
                return $this->MapNew($Expression, $ReturnType);
            
            case $Expression instanceof O\ArrayExpression:
                return $this->MapArray($Expression);
            
            case $Expression instanceof O\FunctionCallExpression:
                return $this->MapFunctionCall($Expression, $ReturnType);
            
            case $Expression instanceof O\Aggregates\AggregateExpression:
                return $this->MapAggregate($Expression);
            
            case $Expression instanceof O\TraversalExpression:
                return $this->MapTraversal($Expression, $ReturnType);
            
            case $Expression instanceof O\BinaryOperationExpression:
                return $this->MapBinaryOperation($Expression);
            
            case $Expression instanceof O\UnaryOperationExpression:
                return $this->MapUnaryOperation($Expression);
            
            case $Expression instanceof O\CastExpression:
                return $this->MapCast($Expression);
                
            case $Expression instanceof O\TernaryExpression:
                return $this->MapTernary($Expression, $ReturnType);
                
            case $Expression instanceof O\PropertyExpression:
                return $this->MapProperty($Expression, $ReturnType);
            
            default:
                throw new Mapping\MappingException(
                        'Unsupported object expression type: %s given',
                        get_class($Expression));
        }
    }
    
    private function MapProperty(O\PropertyExpression $Expression, &$ReturnType = null) {
        return $this->PropertyExpressionResolver->MapProperty($Expression, $ReturnType);
    }
    
    private function MapValue(O\ValueExpression $Expression, &$ReturnType = null) {
        $Value = $Expression->GetValue();
        
        switch (true) {
            case $Value === null:
                return $this->ValueMapper->MapNull();
                
            case is_scalar($Value):
                return $this->ValueMapper->MapScalar($Value);
                
            case is_array($Value):
                return $this->MapArrayValue($Value);
                            
            case is_object($Value):
                $ReturnType = get_class($Value);
                return $this->GetObjectTypeMapper($ReturnType)->MapInstance($Value);
                
            case is_resource($Value):
                return $this->ValueMapper->MapResource($Value);

            default:
                throw new Mapping\MappingException('What?! (%s)', var_export($Value, true));
        }
    }
        
    private function MapTraversal(O\TraversalExpression $TraversalExpression, &$ReturnType = null) {
        $ReturnType = null;
        
        if($TraversalExpression->GetTraversalDepth() === 1) {
            $MappedValueExpression = $this->MapTraversalOriginExpression($TraversalExpression->GetValueExpression(), $ReturnType);
        }
        else {
            $MappedValueExpression = $this->MapTraversal($TraversalExpression->GetValueExpression(), $ReturnType);
        }
        
        if($ReturnType === null) {
            throw new Mapping\MappingException(
                    'Invalid traversal expression tree: unknown return type for %s',
                    get_class($TraversalExpression->GetValueExpression()));
        }
        $ObjectTypeMapper = $this->GetObjectTypeMapper($ReturnType);
        
        switch (true) {
            case $TraversalExpression instanceof O\FieldExpression:
                return $ObjectTypeMapper->MapField(
                        $MappedValueExpression,
                        $TraversalExpression->GetNameExpression(), 
                        $ReturnType);
                
            case $TraversalExpression instanceof O\MethodCallExpression:
                return $ObjectTypeMapper->MapMethodCall(
                        $MappedValueExpression,
                        $TraversalExpression->GetNameExpression(), 
                        $this->MapAll($TraversalExpression->GetArgumentExpressions()), 
                        $ReturnType);
                
            case $TraversalExpression instanceof O\IndexExpression:
                return $ObjectTypeMapper->MapIndex(
                        $MappedValueExpression,
                        $TraversalExpression->GetIndexExpression(), 
                        $ReturnType);
                
            case $TraversalExpression instanceof O\InvocationExpression:
                return $ObjectTypeMapper->MapInvocation(
                        $MappedValueExpression,
                        $this->MapAll($TraversalExpression->GetArgumentExpressions()),
                        $ReturnType);
                
            default:
                throw new Mapping\MappingException(
                        'Unsupported object traversal expression type: %s given',
                        get_class($TraversalExpression));
        }
    }
    
    private function MapTraversalOriginExpression(O\Expression $Expression, &$ReturnType) {
        switch (true) {
            case $Expression instanceof O\ValueExpression:
                return $this->MapValue($Expression, $ReturnType);
                
            case $Expression instanceof O\NewExpression:
                return $this->MapNew($Expression, $ReturnType);
            
            case $Expression instanceof O\FunctionCallExpression:
                return $this->MapFunctionCall($Expression, $ReturnType);
                
            case $Expression instanceof O\PropertyExpression:
                return $this->MapProperty($Expression, $ReturnType);
             
            default:
                throw new Mapping\MappingException(
                        'Unsupported object traversal origin expression type: %s given',
                        get_class($Expression));
        }
    }
    
    private function MapNew(O\NewExpression $Expression, &$ReturnType = null) {
        $ClassTypeExpression = $Expression->GetClassTypeExpression();
        if(!($ClassTypeExpression instanceof O\ValueExpression)) {
            throw new Mapping\MappingException(
                    'Cannot map new expression with unresolved class type: %s given expecting %s',
                    $ClassTypeExpression->GetType(),
                    O\ValueExpression::GetType());
        }
        $ClassType = $ClassTypeExpression->GetValue();
        $MappedArgumentExpressions = $this->MapAll($Expression->GetArgumentExpressions());
        
        $ReturnType = $ClassType;
        return $this->GetObjectTypeMapper($ClassType)->MapNew($MappedArgumentExpressions);
    }
    
    private function MapArrayValue(array $Array) {
        $MappedKeyExpressions = [];
        $MappedValueExpressions = [];
        foreach($Array as $Key => $Value) {
            $MappedKeyExpressions[] = $this->MapValue(O\Expression::Value($Key));
            $MappedValueExpressions[] = $this->MapValue(O\Expression::Value($Value));
        }
        
        return $this->ArrayMapper->MapArrayExpression($MappedKeyExpressions, $MappedValueExpressions);
    }
    
    private function MapArray(O\ArrayExpression $Expression) {
        $MappedKeyExpressions = $this->MapAll($Expression->GetKeyExpressions());
        $MappedValueExpressions = $this->MapAll($Expression->GetValueExpressions());
        
        return $this->ArrayMapper->MapArrayExpression($MappedKeyExpressions, $MappedValueExpressions);
    }
        
    private function MapFunctionCall(O\FunctionCallExpression $Expression, &$ReturnType = null) {
        $FunctionNameExpression = $Expression->GetNameExpression();
        $MappedArgumentExpressions = $this->MapAll($Expression->GetArgumentExpressions());
        
        return $this->FunctionMapper->MapFunctionCall($FunctionNameExpression, $MappedArgumentExpressions, $ReturnType);
    }
        
    private function MapAggregate(O\Aggregates\AggregateExpression $Expression) {
        switch (true) {
            case $Expression instanceof O\Aggregates\AllExpression:
                return $this->AggregateMapper->MapAll($this->Map($Expression->GetValueExpression()));
                
            case $Expression instanceof O\Aggregates\AnyExpression:
                return $this->AggregateMapper->MapAny($this->Map($Expression->GetValueExpression()));
                
            case $Expression instanceof O\Aggregates\AverageExpression:
                return $this->AggregateMapper->MapAverage(
                        $Expression->UniqueValuesOnly(),
                        $this->Map($Expression->GetValueExpression()));
                
            case $Expression instanceof O\Aggregates\CountExpression:
                return $this->AggregateMapper->MapCount(
                        $Expression->HasUniqueValueExpressions() ? $this->MapAll($Expression->GetUniqueValueExpressions()) : null);
                
            case $Expression instanceof O\Aggregates\ImplodeExpression:
                return $this->AggregateMapper->MapImplode(
                        $Expression->UniqueValuesOnly(),
                        $Expression->GetDelimiter(),
                        $this->Map($Expression->GetValueExpression()));
                
            case $Expression instanceof O\Aggregates\MaximumExpression:
                return $this->AggregateMapper->MapMaximum(
                        $this->Map($Expression->GetValueExpression()));
                
            case $Expression instanceof O\Aggregates\MinimumExpression:
                return $this->AggregateMapper->MapMinimum(
                        $this->Map($Expression->GetValueExpression()));
                
            case $Expression instanceof O\Aggregates\SumExpression:
                return $this->AggregateMapper->MapSum(
                        $Expression->UniqueValuesOnly(),
                        $this->Map($Expression->GetValueExpression()));
                
            default:
                throw new Mapping\MappingException(
                        'Unsupported aggregate expression type: %s given',
                        get_class($Expression));
        }
    }
        
    private function MapBinaryOperation(O\BinaryOperationExpression $Expression) {
        $MappedLeftOperandExpression = $this->Map($Expression->GetLeftOperandExpression());
        $Operator = $Expression->GetOperator();
        $MappedRightOperandExpression = $this->Map($Expression->GetRightOperandExpression());
                
        return $this->OperationMapper->MapBinary(
                $MappedLeftOperandExpression, 
                $Operator, 
                $MappedRightOperandExpression);
    }
        
    private function MapUnaryOperation(O\UnaryOperationExpression $Expression) {
        $Operator = $Expression->GetOperator();
        $MappedOperandExpression = $this->Map($Expression->GetOperandExpression());
        
        return $this->OperationMapper->MapUnary(
                $Operator, 
                $MappedOperandExpression);
    }
        
    private function MapCast(O\CastExpression $Expression) {
        $CastType = $Expression->GetCastType();
        $MappedCastValueExpression = $this->Map($Expression->GetCastValueExpression());
        
        return $this->OperationMapper->MapCast(
                $CastType, 
                $MappedCastValueExpression);
    }
        
    private function MapTernary(O\TernaryExpression $Expression, &$ReturnType = null) {
        $MappedConditionExpression = $this->Map($Expression->GetConditionExpression());
        $IfTrueReturnType = null;
        $MappedIfTrueExpression = $this->Map($Expression->GetIfTrueExpression(), $IfTrueReturnType);
        $IfFalseReturnType = null;
        $MappedIfFalseExpression = $this->Map($Expression->GetIfFalseExpression(), $IfFalseReturnType);
        
        if($IfTrueReturnType === $IfFalseReturnType && $IfTrueReturnType !== null) {
            $ReturnType = $IfTrueReturnType;
        }
        
        return $this->ControlFlowMapper->MapTernary(
                $MappedConditionExpression, 
                $MappedIfTrueExpression, 
                $MappedIfFalseExpression);
    }
}

?>