<?php

namespace Storm\Drivers\Fluent\Object;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\ExpressionTree;

class CachingFunctionToExpressionTreeConverter extends FunctionToExpressionTreeConverter {
    private $Cache;
    
    public function __construct(
            ICache $Cache,
            Functional\IParser $Parser) {
        parent::__construct($Parser);
        
        $this->Cache = $Cache;
    }

    public function ConvertAndResolve(Object\IEntityMap $EntityMap, callable $Function) {
        $Reflection = $this->Parser->GetReflection($Function);
        $FunctionHash = $this->FunctionHash($Reflection);
        
        $ExpressionTree = null;
        if($this->Cache->Contains($FunctionHash)) {
            $ExpressionTree = $this->Cache->Retrieve($FunctionHash);
        }
        
        if(!($ExpressionTree instanceof ExpressionTree)) {
            $ExpressionTree = $this->Parser->Parse($Reflection)->GetExpressionTree();
            $ExpressionTree->Simplify();
            $ExpressionTree->ResolveTraversalExpressions($EntityMap);
            
            //Save the expression tree with all the unresolved values so it can be resolved later
            $this->Cache->Save($FunctionHash, $ExpressionTree);
        }
        
        if(!$ExpressionTree->IsResolved()) {
            $ExpressionTree->ResolveVariables($Reflection->getStaticVariables());
            //Simply and resolve any traversal expressions that could not be resolved due unresolved variables
            $ExpressionTree->Simplify();
            $ExpressionTree->ResolveTraversalExpressions($EntityMap);
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