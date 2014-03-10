<?php

namespace Storm\Drivers\Pinq\Object;

use \Storm\Core\Object;

class CachingFunctionToExpressionTreeConverter extends FunctionToExpressionTreeConverter {
    private $Cache;
    
    public function __construct(
            ICache $Cache,
            Functional\IParser $Parser) {
        parent::__construct($Parser);
        
        $this->Cache = $Cache;
    }

    public function ConvertAndResolve(Object\IEntityMap $EntityMap, callable $Function, array $ParameterExpressionMap = []) {
        $Reflection = $this->Parser->GetReflection($Function);
        $FunctionHash = $this->FunctionHash($Reflection);
        
        $ExpressionTree = null;
        if($this->Cache->Contains($FunctionHash)) {
            $ExpressionTree = $this->Cache->Retrieve($FunctionHash);
        }
        
        if(!($ExpressionTree instanceof Functional\ExpressionTree)) {
            $ExpressionTree = $this->Parser->Parse($Reflection)->GetExpressions();
            
            /*
             * Resolve all that can be currently resolved and save the expression tree 
             * with all the unresolvable variables so it can be resolved with different values later
             */
            $ParameterExpressionMap = $this->ParametersToVariableNames($Reflection, $ParameterExpressionMap);
            $this->Resolve($ExpressionTree, $EntityMap, $ParameterExpressionMap);
            $this->Cache->Save($FunctionHash, $ExpressionTree);
        }
        
        if(!$ExpressionTree->IsResolved()) {
            /*
             * Simplify and resolve any remaining expressions that could not be resolved due 
             * to unresolved variables
             */
            $this->Resolve($ExpressionTree, $EntityMap, $Reflection->getStaticVariables());
        }
        
        if(!$ExpressionTree->IsResolved()) {
            throw FluentException::ContainsUnresolvableVariables($Reflection, $ExpressionTree->GetUnresolvedVariables());
        }
        
        return $ExpressionTree;
    }
    
    private function FunctionHash(\ReflectionFunctionAbstract $Reflection) {
        return 'ExpressionTree-' . md5(implode(' ', [$Reflection->getFileName(), $Reflection->getName(), $Reflection->getStartLine(), $Reflection->getEndLine()]));
    }
}

?>