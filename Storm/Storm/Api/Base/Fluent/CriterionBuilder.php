<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Core\Object;
use \Storm\Drivers\Fluent\Object\Criterion;
use \Storm\Drivers\Fluent\Object\Closure;

class CriterionBuilder {
    protected $EntityType;
    protected $EntityMap;
    private $ClosureToASTConverter;
    private $Criterion;
    
    public function __construct(
            Object\EntityMap $EntityMap,
            Closure\ClosureToASTConverter $ClosureToASTConverter) {
        $this->EntityMap = $EntityMap;
        $this->EntityType = $EntityMap->GetEntityType();
        $this->ClosureToASTConverter = $ClosureToASTConverter;
        $this->Criterion = new Criterion($EntityMap->GetEntityType());
    }
    
    final protected function ClosureToExpandedAST(\Closure $Closure) {
        $AST = $this->ClosureToASTConverter->ClosureToAST($this->EntityMap, $Closure);
        if(!$AST->IsResolved()) {
            throw new \Exception('Closure constains unresolved variables: $' . implode(', $', $AST->GetUnresolvedVariables()));
        }
        
        return $AST;
    }
    
    final public function Where(\Closure $PredicateClosure) {
        $this->Criterion->AddPredicateClosure($this->ClosureToExpandedAST($PredicateClosure));        
        return $this;
    }
    
    final public function OrderBy(\Closure $ExpressionClosure) {
        $this->Criterion->AddOrderByClosure($this->ClosureToExpandedAST($ExpressionClosure), true);        
        return $this;
    }
    
    final public function OrderByDescending(\Closure $ExpressionClosure) {
        $this->Criterion->AddOrderByClosure($this->ClosureToExpandedAST($ExpressionClosure), false);        
        return $this;
    }
    
    final public function GroupBy(\Closure $ExpressionClosure) {
        $this->Criterion->AddGroupByClosure($this->ClosureToExpandedAST($ExpressionClosure));        
        return $this;
    }
    
    final public function Skip($Amount) {
        $this->Criterion->SetRangeOffset($Amount);        
        return $this;
    }
    
    final public function Limit($Amount) {
        $this->Criterion->SetRangeAmount($Amount);        
        return $this;
    }
    
    /**
     * @internal
     */
    final public function BuildCriterion() {
        return $this->Criterion;
    }
}

?>