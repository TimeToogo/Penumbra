<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Mapping;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational;
use \Storm\Core\Relational\Expressions as R;

final class ExpressionMapper {
    /**
    * @var Expressions\PropertyExpressionResolver
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
     * @var Expressions\IObjectMapper
     */
    private $ObjectMapper;
    
    /**
     * @var Expressions\IResourceMapper
     */
    private $ResourceMapper;
    
    /**
     * @var Expressions\IControlFlowMapper
     */
    private $ControlFlowMapper;
    
    public function __construct(
            Expressions\PropertyExpressionResolver $PropertyExpressionResolver,
            Expressions\IValueMapper $ValueMapper, 
            Expressions\IArrayMapper $ArrayMapper, 
            Expressions\IOperationMapper $OperationMapper,
            Expressions\IFunctionMapper $FunctionMapper, 
            Expressions\IObjectMapper $ObjectMapper, 
            Expressions\IResourceMapper $ResourceMapper, 
            Expressions\IControlFlowMapper $ControlFlowMapper) {
        $this->PropertyExpressionResolver = $PropertyExpressionResolver;
        $this->ValueMapper = $ValueMapper;
        $this->ArrayMapper = $ArrayMapper;
        $this->OperationMapper = $OperationMapper;
        $this->FunctionMapper = $FunctionMapper;
        $this->ObjectMapper = $ObjectMapper;
        $this->ResourceMapper = $ResourceMapper;
        $this->ControlFlowMapper = $ControlFlowMapper;
    }
    
    final public function MapExpressions(array $Expressions) {
        foreach($Expressions as $Key => $Expression) {
            $Expressions[$Key] = $this->MapExpression($Expression);
        }
        
        return $Expressions;
    }
    
    final public function MapExpression(O\Expression $Expression) {
        switch (true) {
            case $Expression instanceof O\ValueExpression:
                return $this->MapValue($Expression);
           
            case $Expression instanceof O\NewExpression:
                return $this->MapNew($Expression);
            
            case $Expression instanceof O\ArrayExpression:
                return $this->MapArray($Expression);
            
            case $Expression instanceof O\FunctionCallExpression:
                return $this->MapFunctionCall($Expression);
            
            case $Expression instanceof O\TraversalExpression:
                return $this->MapTraversal($Expression);
            
            case $Expression instanceof O\BinaryOperationExpression:
                return $this->MapBinaryOperation($Expression);
            
            case $Expression instanceof O\UnaryOperationExpression:
                return $this->MapUnaryOperation($Expression);
            
            case $Expression instanceof O\CastExpression:
                return $this->MapCast($Expression);
                
            case $Expression instanceof O\TernaryExpression:
                return $this->MapTernary($Expression);
                
            case $Expression instanceof O\PropertyExpression:
                return $this->MapProperty($Expression);
            
            default:
                throw new Mapping\MappingException(
                        'Unsupported object expression type: %s given',
                        get_class($Expression));
        }
    }
    
    private function MapProperty(O\PropertyExpression $Expression) {
        return $this->PropertyExpressionResolver->MapProperty($Expression);
    }
    
    private function MapValue(O\ValueExpression $Expression, O\TraversalExpression $TraversalExpression = null) {
        $Value = $Expression->GetValue();
        
        switch (true) {
            case $Value === null:
                return $this->ValueMapper->MapNull();
                
            case is_scalar($Value):
                return $this->ValueMapper->MapScalar($Value);
                
            case is_array($Value):
                return $this->ArrayMapper->MapArray($Value);
            
            case is_object($Value):
                return $this->ObjectMapper->MapInstance($Value, $TraversalExpression);
                
            case is_resource($Value):
                return $this->ResourceMapper->MapResource($Value);

            default:
                throw new \Storm\Core\Mapping\MappingException('What?! (%s)', var_export($Value, true));
        }
    }
        
    private function MapTraversal(O\TraversalExpression $TraversalExpression) {
        $TraversalOriginExpression = $this->GetTraversalOriginExpression($TraversalExpression);
        
        switch (true) {
            case $TraversalOriginExpression instanceof O\ValueExpression:
                return $this->MapValue($TraversalOriginExpression, $TraversalExpression);
                
            case $TraversalOriginExpression instanceof O\NewExpression:
                return $this->MapNew($TraversalOriginExpression, $TraversalExpression);
                
            case $TraversalOriginExpression instanceof O\ArrayExpression:
                return $this->MapArray($TraversalOriginExpression, $TraversalExpression);
                
            case $TraversalOriginExpression instanceof O\FunctionCallExpression:
                return $this->MapFunctionCall($TraversalOriginExpression, $TraversalExpression);
             
            default:
                throw new Mapping\MappingException(
                        'Unsupported object traversal origin expression type: %s given',
                        get_class($TraversalExpression));
        }
    }
    
    /**
     * @return O\Expression
     */
    private function GetTraversalOriginExpression(O\TraversalExpression $Expression) {
        while ($Expression instanceof O\TraversalExpression) {
            $Expression = $Expression->GetValueExpression();
        }
        
        return $Expression;
    }
    
    private function MapNew(O\NewExpression $Expression, O\TraversalExpression $TraversalExpression = null) {
        $ClassType = $Expression->GetClassType();
        $MappedArgumentExpressions = $this->MapExpressions($Expression->GetArgumentExpressions());
        
        return $this->ObjectMapper->MapNew($ClassType, $MappedArgumentExpressions, $TraversalExpression);
    }
    
    private function MapArray(O\ArrayExpression $Expression, O\TraversalExpression $TraversalExpression = null) {
        $MappedKeyExpressions = $this->MapExpressions($Expression->GetKeyExpressions());
        $MappedValueExpressions = $this->MapExpressions($Expression->GetValueExpressions());
        
        return $this->ArrayMapper->MapArrayExpression($MappedKeyExpressions, $MappedValueExpressions, $TraversalExpression);
    }
        
    private function MapFunctionCall(O\FunctionCallExpression $Expression, O\TraversalExpression $TraversalExpression = null) {
        $FunctionName = $Expression->GetName();
        $MappedArgumentExpressions = $this->MapExpressions($Expression->GetArgumentExpressions());
        
        return $this->FunctionMapper->MapFunctionCall($FunctionName, $MappedArgumentExpressions, $TraversalExpression);
    }
        
    private function MapBinaryOperation(O\BinaryOperationExpression $Expression) {
        $MappedLeftOperandExpression = $this->MapExpression($Expression->GetLeftOperandExpression());
        $Operator = $Expression->GetOperator();
        $MappedRightOperandExpression = $this->MapExpression($Expression->GetLeftOperandExpression());
        
        
        return $this->OperationMapper->MapBinary(
                $MappedLeftOperandExpression, 
                $Operator, 
                $MappedRightOperandExpression);
    }
        
    private function MapUnaryOperation(O\UnaryOperationExpression $Expression) {
        $Operator = $Expression->GetOperator();
        $MappedOperandExpression = $this->MapExpression($Expression->GetOperandExpression());
        
        
        return $this->OperationMapper->MapUnary(
                $Operator, 
                $MappedOperandExpression);
    }
        
    private function MapCast(O\CastExpression $Expression) {
        $CastType = $Expression->GetCastType();
        $MappedCastValueExpression = $this->MapExpression($Expression->GetCastValueExpression());
        
        return $this->OperationMapper->MapCast(
                $CastType, 
                $MappedCastValueExpression);
    }
        
    private function MapTernary(O\TernaryExpression $Expression) {
        $MappedConditionExpression = $this->MapExpression($Expression->GetConditionExpression());
        $MappedIfTrueExpression = $this->MapExpression($Expression->GetIfTrueExpression());
        $MappedIfFalseExpression = $this->MapExpression($Expression->GetIfFalseExpression());
        
        return $this->ControlFlowMapper->MapTernary(
                $MappedConditionExpression, 
                $MappedIfTrueExpression, 
                $MappedIfFalseExpression);
    }
}

?>