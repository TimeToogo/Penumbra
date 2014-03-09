<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Api\Base\Repository;
use \Storm\Core\Object;
use \Storm\Drivers\Pinq\Object\Request;
use \Storm\Drivers\Pinq\Object\Functional\ExpressionTree;
use \Storm\Drivers\Pinq\Object\IFunctionToExpressionTreeConverter;

/**
 * The RequestBuilder provides a fluent interface for building requests
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class RequestBuilder extends CriterionBuilder {
    /**
     * @var Repository
     */
    protected $Repository;
    private $Properties;
    private $GroupByExpresssionTrees = [];
    private $AggregatePredicateExpressionTrees = [];
    private $Properties;
    
    public function __construct(
            Repository $Repository,
            Object\IEntityMap $EntityMap, 
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        parent::__construct($EntityMap, $FunctionToExpressionTreeConverter);
        
        $this->Repository = $Repository;
        $this->EntityMap = $EntityMap;
        $this->Properties = $this->EntityMap->GetProperties();
    }
    
    /**
     * Builds the request from specified parameters
     * 
     * @return Request
     */
    final public function BuildRequest() {
        return new Request(
            $this->EntityMap,
            $this->Properties,
            $this->GroupByExpresssionTrees,
            $this->AggregatePredicateExpressionTrees,
            $this->IsSingleEntity, 
            $this->BuildCriterion());
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
     * @return static
     */
    final public function GroupBy(callable $Expression) {
        $this->GroupByExpresssionTrees[] = $this->FunctionToExpressionTree($Expression);        
        return $this;
    }
        
    /**
     * Specifies a function to parse as the predicate for the groups.
     * 
     * Example expression closure:
     * <code>
     * function (Car $Car) {
     *     return max($Car->GetPrice()) < 50000;
     * }
     * </code>
     * 
     * @param callable $Expression The expression function
     * @return static
     */
    final public function Having(callable $Expression) {
        $this->AggregatePredicateExpressionTrees[] = $this->FunctionToExpressionTree($Expression);        
        return $this;
    }
    
    /**
     * @return object[]
     */
    public function AsArray() {
        return $this->Repository->LoadAsArray($this->BuildRequest());
    }
    
    /**
     * @return object|null
     */
    public function First() {  
        return $this->Repository->LoadFirst($this->BuildRequest());
    }
    
    /**
     * @return int
     */
    public function Count() {
        return $this->Repository->LoadCount($this->BuildRequest());
    }
    
    /**
     * @return boolean
     */
    public function Exists() {
        return $this->Repository->LoadExists($this->BuildRequest());
    }
}

?>