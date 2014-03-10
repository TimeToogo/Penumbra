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
    protected $EntityMap;
    protected $FunctionToExpressionTreeConverter;
    
    private $Criterion;
    
    public function __construct(
            Object\IEntityMap $EntityMap,
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        $this->EntityType = $EntityMap->GetEntityType();
        $this->EntityMap = $EntityMap;
        $this->FunctionToExpressionTreeConverter = $FunctionToExpressionTreeConverter;
        
        $this->Criterion = new Criterion($EntityMap, $FunctionToExpressionTreeConverter);
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
        $this->Criterion->AddPredicateFunction($Predicate);        
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
        $this->Criterion->AddOrderByFunction($Expression, true);
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
        $this->Criterion->AddOrderByFunction($Expression, true);        
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