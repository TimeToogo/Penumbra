<?php

namespace Penumbra\Pinq\Functional;

use \Penumbra\Core\Object;
use \Penumbra\Pinq\PinqException;

class FunctionToExpressionTreeConverter implements IFunctionToExpressionTreeConverter {
    /**
     * @var Functional\IParser 
     */
    protected $Parser;
    
    public function __construct(IParser $Parser) {
        $this->Parser = $Parser;
    }
    
    final public function GetReflection(callable $Function) {
        return $this->Parser->GetReflection($Function);
    }
    
    final public function GetParser() {
        return $this->Parser;
    }
    
    /**
     * @return Functional\ExpressionTree
     */
    public function ConvertAndResolve(
            \ReflectionFunctionAbstract $Reflection, 
            Object\IEntityMap $EntityMap, 
            array $ParameterNameExpressionMap = []) {
        
        $ExpressionTree = new ExpressionTree($this->Parser->Parse($Reflection)->GetExpressions());
        
        //ReflectionFunction::getStaticVariables() returns the used variables for closures
        $this->Resolve($ExpressionTree, $EntityMap, $Reflection->getStaticVariables(), $ParameterNameExpressionMap);
        
        if($ExpressionTree->HasUnresolvedVariables()) {
            throw PinqException::ContainsUnresolvableVariables($Reflection, $ExpressionTree->GetUnresolvedVariables());
        }
        
        return $ExpressionTree;
    }
    
    final protected function Resolve(ExpressionTree $ExpressionTree, Object\IEntityMap $EntityMap, 
            array $VariableValueMap, array $VariableExpressionMap) {
        $ExpressionTree->ResolveVariables($VariableValueMap, $VariableExpressionMap);
        $ExpressionTree->Simplify();
        $ExpressionTree->ResolveAggregateTraversalExpressions();
        $ExpressionTree->ResolveEntityTraversalExpressions($EntityMap);
    }
}

?>