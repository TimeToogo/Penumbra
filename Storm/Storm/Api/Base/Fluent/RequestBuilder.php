<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Api\Base\Repository;
use \Storm\Core\Object;
use \Storm\Core\Object\Expressions as O;
use \Storm\Pinq\EntityRequest;
use \Storm\Pinq\DataRequest;
use \Storm\Pinq\Functional\ExpressionTree;
use \Storm\Pinq\IFunctionToExpressionTreeConverter;

/**
 * The RequestBuilder provides a fluent interface for building requests
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class RequestBuilder extends CriteriaBuilder {
    /**
     * @var Repository
     */
    protected $Repository;
    private $Properties;
    private $DataFunctionOrExpression = null;
    private $GroupByFunctions = [];
    private $AggregatePredicateFunctions = [];
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
        if($this->DataFunctionOrExpression === null) {
            return new EntityRequest(
                $this->EntityMap,
                $this->FunctionToExpressionTreeConverter,
                $this->Properties,
                $this->GroupByFunctions,
                $this->AggregatePredicateFunctions,
                $this->BuildCriteria());
        }
        else {
            return new DataRequest(
                $this->EntityMap,
                $this->FunctionToExpressionTreeConverter,
                $this->DataFunctionOrExpression,
                $this->GroupByFunctions,
                $this->AggregatePredicateFunctions,
                $this->BuildCriteria());
        }
    }
    
    /**
     * Specifies the function to use as grouping for the criteria.
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
        $this->GroupByFunctions[] = $this->FunctionToExpressionTree($Expression);        
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
        $this->AggregatePredicateFunctions[] = $this->FunctionToExpressionTree($Expression);        
        return $this;
    }
    
    /**
     * @return object[]
     */
    public function AsArray() {
        return $this->Repository->LoadEntities($this->BuildRequest());
    }
    
    /**
     * @return object|null
     */
    public function First() {
        $this->Limit(1);
        $Entities = $this->Repository->LoadEntities($this->BuildRequest());
        
        return count($Entities) === 0 ? null : reset($Entities);
    }
    
    /**
     * @return int|int[]
     */
    public function Count() {
        
        return $this->Repository->LoadData($this->BuildRequest());
    }
    
    /**
     * @return boolean
     */
    public function Exists() {
        return $this->Repository->LoadExists($this->BuildRequest());
    }
    
    /**
     * @return boolean
     */
    public function Data(callable $DataFunction) {
        $this->DataFunctionOrExpression = $DataFunction;
        return $this->Repository->LoadData($this->BuildRequest());
    }
    
    private function LoadAggregateExpression(O\Expression $Expression) {
        $this->DataFunctionOrExpression = O\Expression::NewArray([O\Expression::Value(0)], $Expression);
    }
}

?>