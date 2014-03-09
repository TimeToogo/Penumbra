<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Core\Object;
use \Storm\Drivers\Pinq\Object\Criterion;
use \Storm\Drivers\Pinq\Object\Functional\ExpressionTree;
use \Storm\Drivers\Pinq\Object\IFunctionToExpressionTreeConverter;

/**
 * The CriterionBuilder provides a fluent interface for building criteria
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CriterionBuilder {
    protected $EntityType;
    /**
     * @var Object\IEntityMap
     */
    protected $EntityMap;
    private $FunctionToExpressionTreeConverter;
    private $Criterion;
    
    public function __construct(
            Object\IEntityMap $EntityMap,
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        $this->EntityMap = $EntityMap;
        $this->EntityType = $EntityMap->GetEntityType();
        $this->FunctionToExpressionTreeConverter = $FunctionToExpressionTreeConverter;
        $this->Criterion = new Criterion($EntityMap->GetEntityType());
    }
    
    /**
     * Returns the built criterion
     * 
     * @return Criterion The specified criterion
     */
    final public function BuildCriterion() {
        return $this->Criterion;
    }
    
    /**
     * Parses a given function into an expression tree structure using the provided converter
     * 
     * @param callable $Function The function to parse
     * 
     * @return ExpressionTree The resolved function's expression tree
     */
    final protected function FunctionToExpressionTree(callable $Function) {
        $EpresstionTree = $this->FunctionToExpressionTreeConverter->ConvertAndResolve($this->EntityMap, $Function);
        
        return $EpresstionTree;
    }
    
    /**
     * Specifies a function to parse as predicate for this criterion.
     * 
     * Example predicate closure:
     * <code>
     *  function (Car $Car) use ($Name) {
     *      return $Car->IsAvailable() && $Car->GetName() === $Name;
     *  }
     * </code>
     * 
     * @param callable $Predicate The predicate function
     * @return static 
     */
    final public function Where(callable $Predicate) {
        $this->Criterion->AddPredicateExpression($this->FunctionToExpressionTree($Predicate));        
        return $this;
    }
    
    /**
     * Specifies the function to use as an ascending ordering for the criterion.
     * 
     * Example expression closure:
     * <code>
     * function (Car $Car) {
     *     return $Car->GetManufactureDate();
     * }
     * </code>
     * 
     * @param callable $Expression The expression closure
     * @return static
     */
    final public function OrderBy(callable $Expression) {
        $this->Criterion->AddOrderByExpression($this->FunctionToExpressionTree($Expression), true);        
        return $this;
    }
    
    /**
     * Specifies the function to use as an descending ordering for the criterion.
     * 
     * Example expression closure:
     * <code>
     * function (Car $Car) {
     *     return $Car->GetManufactureDate();
     * }
     * </code>
     * 
     * @param callable $Expression The expression closure
     * @return static
     */
    final public function OrderByDescending(callable $Expression) {
        $this->Criterion->AddOrderByExpression($this->FunctionToExpressionTree($Expression), false);        
        return $this;
    }
    
    /**
     * Specifies the amount of entities to skip.
     * 
     * @param int $Amount The amount of entities to skip
     * @return static
     */
    final public function Skip($Amount) {
        $this->Criterion->SetRangeOffset($Amount);        
        return $this;
    }
    
    
    /**
     * Specifies the amount of entities to retrieve. Pass null to remove limit.
     * 
     * @param int|null $Amount The amount of entities to retrieve
     * @return static
     */
    final public function Limit($Amount) {
        $this->Criterion->SetRangeAmount($Amount);        
        return $this;
    }
}

?>