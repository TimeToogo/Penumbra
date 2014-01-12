<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Core\Object;
use \Storm\Drivers\Intelligent\Object\Pinq;
use \Storm\Drivers\Intelligent\Object\Closure;

class CriterionBuilder {
    private $EntityMap;
    private $ClosureReader;
    private $ClosureParser;
    private $Criterion;
    
    public function __construct(
            Object\EntityMap $EntityMap,
            Closure\IReader $ClosureReader,
            Closure\IParser $ClosureParser) {
        $this->EntityMap = $EntityMap;
        $this->ClosureReader = $ClosureReader;
        $this->ClosureParser = $ClosureParser;
        $this->Criterion = new Pinq\Criterion($EntityMap->GetEntityType());
    }
    
    /**
     * @return CriterionBuilder
     */
    final public function Where(\Closure $PredicateClosure) {
        $this->Criterion->AddPredicateClosure($PredicateClosure);        
        return $this;
    }
    
    final public function OrderBy(\Closure $ExpressionClosure) {
        $this->Criterion->AddOrderByClosure($ExpressionClosure, true);        
        return $this;
    }
    
    final public function OrderByDescending(\Closure $ExpressionClosure) {
        $this->Criterion->AddOrderByClosure($ExpressionClosure, false);        
        return $this;
    }
    
    final public function GroupBy(\Closure $ExpressionClosure) {
        $this->Criterion->AddGroupByClosure($ExpressionClosure);        
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