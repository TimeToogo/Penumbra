<?php

namespace Storm\Pinq\Functional;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions as O;

/**
 * Acts as a mutable state for the underlying expressions of a function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExpressionTree {
    
    /**
     * The expressions in this expression tree 
     * 
     * @var O\Expression[]
     */
    private $Expressions = [];
    
    /**
     * @var O\ReturnExpression |null
     */
    private $ReturnExpression = null;
    
    private $VariableResolverWalker;
    private $EntityTraversalResolverWalker;
    private $AggregateTraversalResolverWalker;
    
    public function __construct(array $Expressions) {
        $this->Expressions = $Expressions;
        
        $this->VariableResolverWalker = new Walkers\VariableResolverWalker();
        $this->EntityTraversalResolverWalker = new Walkers\EntityTraversalResolverWalker();
        $this->AggregateTraversalResolverWalker = new Walkers\AggregateTraversalResolverWalker();
        
        $this->LoadReturnExpression();
    }
    
    /**
     * @return O\Expression[]
     */
    final public function GetExpressions() {
        return $this->Expressions;
    }
    
    /**
     * @return boolean
     */
    final public function HasReturnExpression() {
        return $this->ReturnExpression !== null;
    }
    
    /**
     * @return O\ReturnExpression|null
     */
    final public function GetReturnExpression() {
        return $this->ReturnExpression;
    }
    
    final public function ResolveEntityTraversalExpressions(Object\IEntityMap $EntityMap) {
        $this->EntityTraversalResolverWalker->SetEntityMap($EntityMap);
        $this->Expressions = $this->EntityTraversalResolverWalker->WalkAll($this->Expressions);
        $this->LoadReturnExpression();
    }
    
    final public function ResolveAggregateTraversalExpressions() {
        $this->Expressions = $this->AggregateTraversalResolverWalker->WalkAll($this->Expressions);
        $this->LoadReturnExpression();
    }
    
    final public function Simplify() {
        foreach ($this->Expressions as $Key => $Expression) {
            $this->Expressions[$Key] = $Expression->Simplify();
        }
        $this->LoadReturnExpression();
    }
    
    final public function HasUnresolvedVariables() {
        return $this->VariableResolverWalker->HasUnresolvedVariables();
    }
    
    final public function GetUnresolvedVariables() {
        return $this->VariableResolverWalker->GetUnresolvedVariables();
    }
    
    final public function ResolveVariables(array $VariableValueMap, array $VariableExpressionMap = []) {
        foreach($VariableValueMap as $VariableName => $Value) {
            $VariableValueMap[$VariableName] = O\Expression::Value($Value);
        }
        
        $this->ResolveVariablesToExpressions($VariableExpressionMap + $VariableValueMap);
    }
    
    final public function ResolveVariablesToExpressions(array $VariableExpressionMap) {
        $this->VariableResolverWalker->ResetUnresolvedVariables();
        $this->VariableResolverWalker->SetVariableResolutionMap($VariableExpressionMap);
        $this->Expressions = $this->VariableResolverWalker->WalkAll($this->Expressions);
        $this->LoadReturnExpression();
    }
    
    final protected function LoadReturnExpression() {
        $this->ReturnExpression = null;
        foreach ($this->Expressions as $Expression) {
            if($Expression instanceof O\ReturnExpression) {
                $this->ReturnExpression = $Expression;
            }
        }
    }
}

?>