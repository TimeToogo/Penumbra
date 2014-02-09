<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Core\Object;
use \Storm\Api\Base\ClosureToASTConverter;
use \Storm\Drivers\Fluent\Object\Criterion;
use \Storm\Drivers\Fluent\Object\Closure;

/**
 * The CriterionBuilder provides a fluent interface for building criteria
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CriterionBuilder {
    protected $EntityType;
    protected $EntityMap;
    private $ClosureToASTConverter;
    private $Criterion;
    
    public function __construct(
            Object\IEntityMap $EntityMap,
            ClosureToASTConverter $ClosureToASTConverter) {
        $this->EntityMap = $EntityMap;
        $this->EntityType = $EntityMap->GetEntityType();
        $this->ClosureToASTConverter = $ClosureToASTConverter;
        $this->Criterion = new Criterion($EntityMap->GetEntityType());
    }
    
    /**
     * Parses a given closure into an IAST structure using the provider converter
     * 
     * @param \Closure $Closure The closure to parse
     * 
     * @return \Storm\Drivers\Fluent\Object\Closure\IAST The return
     * 
     * @throws \Storm\Api\Base\InvalidClosureException
     */
    final protected function ClosureToExpandedAST(\Closure $Closure) {
        $AST = $this->ClosureToASTConverter->ClosureToAST($this->EntityMap, $Closure);
        if(!$AST->IsResolved()) {
            throw new \Storm\Api\Base\InvalidClosureException($Closure, 'Contains unresolvable variables: $' . implode(', $', $AST->GetUnresolvedVariables()));
        }
        
        return $AST;
    }
    
    /**
     * Specifies a closure to parse as predicate for this criterion.
     * 
     * Example predicate closure:
     * <code>
     *  function (Car $Car) use ($Name) {
     *      return $Car->IsAvailable() && $Car->GetName() === $Name;
     *  }
     * </code>
     * 
     * @param \Closure $PredicateClosure The predicate closure
     * @return CriterionBuilder 
     */
    final public function Where(\Closure $PredicateClosure) {
        $this->Criterion->AddPredicateClosure($this->ClosureToExpandedAST($PredicateClosure));        
        return $this;
    }
    
    /**
     * Specifies the closure to use as an ascending ordering for the criterion.
     * 
     * Example expression closure:
     * <code>
     * function (Car $Car) {
     *     return $Car->GetManufactureDate();
     * }
     * </code>
     * 
     * @param \Closure $ExpressionClosure The expression closure
     * @return CriterionBuilder
     */
    final public function OrderBy(\Closure $ExpressionClosure) {
        $this->Criterion->AddOrderByClosure($this->ClosureToExpandedAST($ExpressionClosure), true);        
        return $this;
    }
    
    /**
     * Specifies the closure to use as an descending ordering for the criterion.
     * 
     * Example expression closure:
     * <code>
     * function (Car $Car) {
     *     return $Car->GetManufactureDate();
     * }
     * </code>
     * 
     * @param \Closure $ExpressionClosure The expression closure
     * @return CriterionBuilder
     */
    final public function OrderByDescending(\Closure $ExpressionClosure) {
        $this->Criterion->AddOrderByClosure($this->ClosureToExpandedAST($ExpressionClosure), false);        
        return $this;
    }
        
    /**
     * Specifies the closure to use as grouping for the criterion.
     * 
     * Example expression closure:
     * <code>
     * function (Car $Car) {
     *     return $Car->GetBrand();
     * }
     * </code>
     * 
     * @param \Closure $ExpressionClosure The expression closure
     * @return CriterionBuilder
     */
    final public function GroupBy(\Closure $ExpressionClosure) {
        $this->Criterion->AddGroupByClosure($this->ClosureToExpandedAST($ExpressionClosure));        
        return $this;
    }
    
    /**
     * Specifies the amount of entities to skip.
     * 
     * @param int $Amount The amount of entities to skip
     * @return CriterionBuilder
     */
    final public function Skip($Amount) {
        $this->Criterion->SetRangeOffset($Amount);        
        return $this;
    }
    
    
    /**
     * Specifies the amount of entities to retrieve. Pass null to remove limit.
     * 
     * @param int|null $Amount The amount of entities to retrieve
     * @return CriterionBuilder
     */
    final public function Limit($Amount) {
        $this->Criterion->SetRangeAmount($Amount);        
        return $this;
    }
    
    /**
     * Returns the built criterion
     * 
     * @return Criterion The specified criterion
     */
    final public function BuildCriterion() {
        return $this->Criterion;
    }
}

?>