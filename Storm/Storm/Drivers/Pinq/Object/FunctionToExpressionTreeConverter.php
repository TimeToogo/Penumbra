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
    public function ConvertAndResolve(Object\IEntityMap $EntityMap, callable $Function) {
        $Reflection = $this->Parser->GetReflection($Function);
        $ExpressionTree = $this->Parser->Parse($Reflection)->GetExpressionTree();
        /**
         * ReflectionFunction::getStaticVariables() returns  
         */
        $this->Resolve($ExpressionTree, $EntityMap, $Reflection->getStaticVariables());
        
        if(!$ExpressionTree->IsResolved()) {
            throw FluentException::ContainsUnresolvableVariables($Reflection, $ExpressionTree->GetUnresolvedVariables());
        }
        
        return $ExpressionTree;
    }
    
    final protected function Resolve(Functional\ExpressionTree $ExpressionTree, Object\IEntityMap $EntityMap, array $VariableValueMap) {
        $ExpressionTree->ResolveVariables($VariableValueMap);
        $ExpressionTree->Simplify();
        $ExpressionTree->ResolveTraversalExpressions($EntityMap);
    }
}

?>