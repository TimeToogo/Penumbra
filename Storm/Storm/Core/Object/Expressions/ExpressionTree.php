<?php

namespace Storm\Core\Object\Expressions;

use \Storm\Core\Object;

/**
 * Acts as a mutable state for the underlying expressions of a function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExpressionTree {
    /**
     * The expressions in this expression tree 
     * 
     * @var Expression[]
     */
    private $Expressions = [];
    
    /**
     * @var ReturnExpression |null
     */
    private $ReturnExpression = null;
    
    private $UnresolvedValueWalker;
    private $TraversalResolverWalker;
    
    public function __construct(array $Expressions) {
        $this->Expressions = $Expressions;
        $this->UnresolvedValueWalker = new Walkers\UnresolvedValueWalker();
        $this->TraversalResolverWalker = new Walkers\TraversalResolverWalker();
        $this->LoadReturnExpression();
    }
    
    /**
     * @return Expression[]
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
     * @return ReturnExpression|null
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
        return $this->UnresolvedValueWalker->HasAnyUnresolvedValues();
    }
    
    public function GetUnresolvedVariables() {
        return $this->UnresolvedValueWalker->GetUnresolvedVariables();
    }
    
    public function ResolveVariables(array $VariableValueMap) {
        $this->UnresolvedValueWalker->ResetResolvedVariables();
        $this->UnresolvedValueWalker->SetVariableValueMap($VariableValueMap);
        $this->Expressions = $this->UnresolvedValueWalker->WalkAll($this->Expressions);
        $this->LoadReturnExpression();
    }
    
    private function LoadReturnExpression() {
        $this->ReturnExpression = null;
        foreach ($this->Expressions as $Expression) {
            if($Expression instanceof ReturnExpression) {
                $this->ReturnExpression = $Expression;
            }
        }
    }
}

?>