<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Core\Object;
use \Storm\Api\Base\FunctionToASTConverter;
use \Storm\Drivers\Fluent\Object\Criterion;
use \Storm\Drivers\Fluent\Object\Functional;

/**
 * The CriterionBuilder provides a fluent interface for building criteria
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CriterionBuilder {
    protected $EntityType;
    protected $EntityMap;
    private $FunctionToASTConverter;
    private $Criterion;
    
    public function __construct(
            Object\IEntityMap $EntityMap,
            FunctionToASTConverter $FunctionToASTConverter) {
        $this->EntityMap = $EntityMap;
        $this->EntityType = $EntityMap->GetEntityType();
        $this->FunctionToASTConverter = $FunctionToASTConverter;
        $this->Criterion = new Criterion($EntityMap->GetEntityType());
    }
    
    /**
     * Parses a given function into an IAST structure using the provider converter
     * 
     * @param callable $Function The function to parse
     * 
     * @return \Storm\Drivers\Fluent\Object\Functional\IAST The return
     * 
     * @throws \Storm\Api\Base\InvalidFunctionException
     */
    final protected function FunctionToExpandedAST(callable $Function) {
        $AST = $this->FunctionToASTConverter->FunctionToAST($this->EntityMap, $Function);
        
        return $AST;
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
     * @return CriterionBuilder 
     */
    final public function Where(callable $Predicate) {
        $this->Criterion->AddPredicateClosure($this->FunctionToExpandedAST($Predicate));        
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
     * @return CriterionBuilder
     */
    final public function OrderBy(callable $Expression) {
        $this->Criterion->AddOrderByClosure($this->FunctionToExpandedAST($Expression), true);        
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
     * @return CriterionBuilder
     */
    final public function OrderByDescending(callable $Expression) {
        $this->Criterion->AddOrderByClosure($this->FunctionToExpandedAST($Expression), false);        
        return $this;
    }
        
    /**
     * Specifies the function to use as grouping for the criterion.
     * 
     * Example expression closure:
     * <code>
     * function (Car $Car) {
     *     return $Car->GetBrand();
     * }
     * </code>
     * 
     * @param callable $Expression The expression function
     * @return CriterionBuilder
     */
    final public function GroupBy(callable $Expression) {
        $this->Criterion->AddGroupByClosure($this->FunctionToExpandedAST($Expression));        
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