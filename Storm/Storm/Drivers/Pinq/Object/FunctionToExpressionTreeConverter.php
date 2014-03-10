<?php

namespace Storm\Drivers\Pinq\Object;

use \Storm\Core\Object;

class FunctionToExpressionTreeConverter implements IFunctionToExpressionTreeConverter {
    /**
     * @var Functional\IParser 
     */
    protected $Parser;
    
    public function __construct(Functional\IParser $Parser) {
        $this->Parser = $Parser;
    }

    
    final public function GetParser() {
        return $this->Parser;
    }
    
    /**
     * @return Functional\ExpressionTree
     */
    public function ConvertAndResolve(Object\IEntityMap $EntityMap, callable $Function, array $ParameterExpressionMap = []) {
        $Reflection = $this->Parser->GetReflection($Function);
        
        $ExpressionTree = new Functional\ExpressionTree($this->Parser->Parse($Reflection)->GetExpressions());
        
        $ParameterExpressionMap = $this->ParametersToVariableNames($Reflection, $ParameterExpressionMap);
        //ReflectionFunction::getStaticVariables() returns  the used variable for closures
        $this->Resolve($ExpressionTree, $EntityMap, $ParameterExpressionMap + $Reflection->getStaticVariables());
        
        if(!$ExpressionTree->IsResolved()) {
            throw FluentException::ContainsUnresolvableVariables($Reflection, $ExpressionTree->GetUnresolvedVariables());
        }
        
        return $ExpressionTree;
    }
    
    final protected function ParametersToVariableNames(\ReflectionFunctionAbstract $Reflection, array $ParameterExpressionMap) {
        $VariableExpressionMap = [];
        foreach($Reflection->getParameters() as $Key => $Parameter) {
            if(isset($ParameterExpressionMap[$Key])) {
                $VariableExpressionMap[$Parameter->getName()] = $ParameterExpressionMap[$Key];
            }
        }
        
        return $VariableExpressionMap;
    }
    
    final protected function Resolve(Functional\ExpressionTree $ExpressionTree, Object\IEntityMap $EntityMap, array $VariableValueMap) {
        $ExpressionTree->ResolveVariables($VariableValueMap);
        $ExpressionTree->Simplify();
        $ExpressionTree->ResolveTraversalExpressions($EntityMap);
    }
}

?>