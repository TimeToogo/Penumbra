<?php

namespace Storm\Drivers\Pinq\Object\Functional;

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
    private $TraversalResolverWalker;
    
    public function __construct(array $Expressions) {
        $this->Expressions = $Expressions;
        $this->VariableResolverWalker = new Walkers\VariableResolverWalker();
        $this->TraversalResolverWalker = new Walkers\TraversalResolverWalker();
        $this->LoadReturnExpression();
    }
    
    /**
     * @return O\Expression[]
     */
    public function GetExpressions() {
        return $this->Expressions;
    }
    
    /**
     * @return boolean
     */
    public function HasReturnExpression() {
        return $this->ReturnExpression !== null;
    }
    
    /**
     * @return O\ReturnExpression|null
     */
    public function GetReturnExpression() {
        return $this->ReturnExpression;
    }
    
    public function ResolveTraversalExpressions(Object\IEntityMap $EntityMap) {
        $this->TraversalResolverWalker->SetEntityMap($EntityMap);
        $this->Expressions = $this->TraversalResolverWalker->WalkAll($this->Expressions);
        $this->LoadReturnExpression();
    }
    
    public function Simplify() {
        foreach ($this->Expressions as $Key => $Expression) {
            $this->Expressions[$Key] = $Expression->Simplify();
        }
        $this->LoadReturnExpression();
    }
    
    public function IsResolved() {
        return $this->VariableResolverWalker->HasAnyUnresolvedValues();
    }
    
    public function GetUnresolvedVariables() {
        return $this->VariableResolverWalker->GetUnresolvedVariables();
    }
    
    public function ResolveVariables(array $VariableValueMap) {
        $this->VariableResolverWalker->ResetUnresolvedVariables();
        $this->VariableResolverWalker->SetVariableValueMap($VariableValueMap);
        $this->Expressions = $this->VariableResolverWalker->WalkAll($this->Expressions);
        $this->LoadReturnExpression();
    }
    
    private function LoadReturnExpression() {
        $this->ReturnExpression = null;
        foreach ($this->Expressions as $Expression) {
            if($Expression instanceof O\ReturnExpression) {
                $this->ReturnExpression = $Expression;
            }
        }
    }
}

?>