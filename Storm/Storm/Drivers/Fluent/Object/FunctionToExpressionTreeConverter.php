<?php

namespace Storm\Drivers\Fluent\Object;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\ExpressionTree;

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
     * @return ExpressionTree
     */
    public function ConvertAndResolve(Object\IEntityMap $EntityMap, callable $Function) {
        $Reflection = $this->Parser->GetReflection($Function);
        $ExpressionTree = $this->Parser->Parse($Reflection)->GetExpressionTree();
        $ExpressionTree->ResolveTraversalExpressions($EntityMap);
        $ExpressionTree->ResolveVariables($Reflection->getStaticVariables());
        $ExpressionTree->Simplify();
        
        if(!$ExpressionTree->IsResolved()) {
            throw FluentException::ContainsUnresolvableVariables($Reflection, $ExpressionTree->GetUnresolvedVariables());
        }
        
        return $ExpressionTree;
    }
}

?>