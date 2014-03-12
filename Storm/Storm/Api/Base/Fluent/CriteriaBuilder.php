<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Core\Object;
use \Storm\Pinq\Criteria;
use \Storm\Pinq\Functional\ExpressionTree;
use \Storm\Pinq\IFunctionToExpressionTreeConverter;

/**
 * The CriteriaBuilder provides a fluent interface for building criteria
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CriteriaBuilder {
    protected $EntityType;
    protected $EntityMap;
    protected $FunctionToExpressionTreeConverter;
    
    private $Criteria;
    
    public function __construct(
            Object\IEntityMap $EntityMap,
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        $this->EntityType = $EntityMap->GetEntityType();
        $this->EntityMap = $EntityMap;
        $this->FunctionToExpressionTreeConverter = $FunctionToExpressionTreeConverter;
        
        $this->Criteria = new Criteria($EntityMap, $FunctionToExpressionTreeConverter);
    }
    
    /**
     * Returns the built criteria
     * 
     * @return Criteria The specified criteria
     */
    final public function BuildCriteria() {
        return $this->Criteria;
    }
    
    /**
     * Specifies a function to parse as predicate for this criteria.
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
        $this->Criteria->AddPredicateFunction($Predicate);        
        return $this;
    }
    
    /**
     * Specifies the function to use as an ascending ordering for the criteria.
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
        $this->Criteria->AddOrderByFunction($Expression, true);
        return $this;
    }
    
    /**
     * Specifies the function to use as an descending ordering for the criteria.
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
        $this->Criteria->AddOrderByFunction($Expression, true);        
        return $this;
    }
    
    /**
     * Specifies the amount of entities to skip.
     * 
     * @param int $Amount The amount of entities to skip
     * @return static
     */
    final public function Skip($Amount) {
        $this->Criteria->SetRangeOffset($Amount);        
        return $this;
    }
    
    
    /**
     * Specifies the amount of entities to retrieve. Pass null to remove limit.
     * 
     * @param int|null $Amount The amount of entities to retrieve
     * @return static
     */
    final public function Limit($Amount) {
        $this->Criteria->SetRangeAmount($Amount);        
        return $this;
    }
}

?>