<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Core\Object;
use \Storm\Drivers\Fluent\Object\Request;
use \Storm\Core\Object\Expressions\ExpressionTree;
use \Storm\Drivers\Fluent\Object\IFunctionToExpressionTreeConverter;

/**
 * The RequestBuilder provides a fluent interface for building requests
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class RequestBuilder extends CriterionBuilder {
    private $Properties;
    private $GroupByExpresssionTrees = [];
    private $AggregatePredicateExpressionTrees = [];
    private $Properties;
    private $IsSingleEntity;
    
    public function __construct(
            Object\IEntityMap $EntityMap, 
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        parent::__construct($EntityMap, $FunctionToExpressionTreeConverter);
        
        $this->EntityMap = $EntityMap;
        $this->Properties = $this->EntityMap->GetProperties();
        $this->IsSingleEntity = false;
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
     * Sets the request to return only the first entity or null if 
     * it does not exists.
     * 
     * @return RequestBuilder
     */
    public function First() {
        $this->IsSingleEntity = true;
        $this->Limit(1);
        
        return $this;
    }
    
    /**
     * Sets the request to return the retrieved entities as an array
     * 
     * @return RequestBuilder
     */
    public function AsArray() {  
        $this->IsSingleEntity = false;
        
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
     * @return RequestBuilder
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
     * @return RequestBuilder
     */
    final public function Having(callable $Expression) {
        $this->AggregatePredicateExpressionTrees[] = $this->FunctionToExpressionTree($Expression);        
        return $this;
    }
}

?>