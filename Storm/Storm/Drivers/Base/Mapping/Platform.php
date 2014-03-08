<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational;
use \Storm\Core\Relational\Expressions as R;

class Platform implements IPlatform {
    
    /**
     * @var Relational\IPlatform
     */
    private $RelationalPlatform;
    
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
            Relational\IPlatform $RelationalPlatform,
            Expressions\IValueMapper $ValueMapper, 
            Expressions\IArrayMapper $ArrayMapper, 
            Expressions\IOperationMapper $OperationMapper,
            Expressions\IFunctionMapper $FunctionMapper, 
            Expressions\IObjectMapper $ObjectMapper, 
            Expressions\IResourceMapper $ResourceMapper, 
            Expressions\IControlFlowMapper $ControlFlowMapper) {
        $this->RelationalPlatform = $RelationalPlatform;
        $this->ValueMapper = $ValueMapper;
        $this->ArrayMapper = $ArrayMapper;
        $this->OperationMapper = $OperationMapper;
        $this->FunctionMapper = $FunctionMapper;
        $this->ObjectMapper = $ObjectMapper;
        $this->ResourceMapper = $ResourceMapper;
        $this->ControlFlowMapper = $ControlFlowMapper;
    }
    
    public function GetRelationalPlatform() {
        return $this->RelationalPlatform;
    }

    final public function GetValueMapper() {
        return $this->ValueMapper;
    }
    
    final public function GetArrayMapper() {
        return $this->ArrayMapper;
    }

    final public function GetOperationMapper() {
        return $this->OperationMapper;
    }

    final public function GetFunctionMapper() {
        return $this->FunctionMapper;
    }

    final public function GetObjectMapper() {
        return $this->ObjectMapper;
    }
    
    final public function GetResourceMapper() {
        return $this->ResourceMapper;
    }
    
    final public function GetControlFlowMapper() {
        return $this->ControlFlowMapper;
    }
    
    final public function MapExpressions(array $Expressions, Expressions\PropertyExpressionResolver $PropertyExpressionResolver) {
        return $this->GetExpressionMapper($PropertyExpressionResolver)->MapExpressions($Expressions);
    }
    
    final public function MapExpression(O\Expression $Expression, Expressions\PropertyExpressionResolver $PropertyExpressionResolver) {
        return $this->GetExpressionMapper($PropertyExpressionResolver)->MapExpression($Expression);
    }
    
    private function GetExpressionMapper(Expressions\PropertyExpressionResolver $PropertyExpressionResolver) {
        return new ExpressionMapper(
                $PropertyExpressionResolver, 
                $this->ValueMapper, 
                $this->ArrayMapper, 
                $this->OperationMapper, 
                $this->FunctionMapper, 
                $this->ObjectMapper, 
                $this->ResourceMapper, 
                $this->ControlFlowMapper);
    }
}

?>