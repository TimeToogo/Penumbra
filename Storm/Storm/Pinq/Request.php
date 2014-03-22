<?php

namespace Storm\Pinq;

use \Storm\Core\Object;
use \Storm\Core\Object\IEntityMap;
use \Storm\Api\IEntityManager;
use \Storm\Drivers\Base\Object\EntityRequest;
use \Storm\Drivers\Base\Object\DataRequest;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Object\Expressions\Aggregates as A;

class Request extends Criteria implements IQueryable  {
    /**
     * @var IEntityManager
     */
    protected $EntityManager;
    
    private $GroupByFunctions = [];
    private $HavingFunctions = [];
    
    /**
     * @var Object\IEntityRequest|null
     */
    private $SubEntityRequest;
    
    public function __construct(
            IEntityManager $EntityManager, 
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        parent::__construct($EntityManager->GetEntityMap(), $FunctionToExpressionTreeConverter);
        
        $this->EntityManager = $EntityManager;
    }
    
    /**
     * @return static
     */
    public function ClearQuery() {
        $this->GroupByFunctions = [];
        $this->HavingFunctions = [];
        $this->SubEntityRequest = null;
        return parent::ClearQuery();
    }
    
    public function From(IQueryable $Query) {
        if(!($Query instanceof self)) {
            throw new PinqException(
                    'Cannot set from queryable: supplied queryable of type %s, expecting %s',
                    get_class($Query),
                    __CLASS__);
        }
        if($Query->EntityType !== $this->EntityType) {
            throw new PinqException(
                    'Cannot set from queryable: supplied request of entity type %s, expecting %s',
                    $Query->EntityTyp,
                    $this->EntityType);
        }
        
        $this->SubEntityRequest = $Query->BuildEntityRequest($this->EntityMap->GetProperties());
        
        return $this;
    }
    
    /**
     * @return static
     */
    public function GroupBy(callable $Function) {
        $this->GroupByFunctions[] = $Function;
        return $this;
    }

    /**
     * @return static
     */
    public function Having(callable $Function) {
        $this->HavingFunctions[] = $Function;
        return $this;
    }
    
    private function BuildEntityRequest(array $Properties) {
        return new EntityRequest(
                $this->EntityType, 
                $Properties, 
                $this->GetGroupByExpressions($this->GroupByFunctions), 
                $this->GetAggregatePredicateExpressions($this->HavingFunctions), 
                $this->BuildCriteria(),
                $this->SubEntityRequest);
    }
    
    private function BuildDataRequest(array $AliasExpressionMap) {
        return new DataRequest(
                $this->EntityType, 
                $AliasExpressionMap, 
                $this->GetGroupByExpressions($this->GroupByFunctions), 
                $this->GetAggregatePredicateExpressions($this->HavingFunctions), 
                $this->BuildCriteria(),
                $this->SubEntityRequest);
    }
    
    private function GetGroupByExpressions($GroupByFunctions) {
        $GroupByExpressions = [];
        foreach($GroupByFunctions as $Key => $GroupByFunction) {
            $GroupByExpressions[$Key] = 
                    $this->GetReturnExpression($this->GetEntityFunctionExpressionTree($GroupByFunction));
        }
        
        return $GroupByExpressions;
    }
    
    private function GetAggregatePredicateExpressions($HavingFunctions) {
        $AggregatePredicates = [];
        foreach($HavingFunctions as $Key => $HavingFunction) {
            $AggregatePredicates[$Key] = 
                    $this->GetReturnExpression($this->GetAggregateFunctionExpressionTree($HavingFunction));
        }
        
        return $AggregatePredicates;
    }
    
    public function AsArray() {
        $Entities = $this->EntityManager->LoadEntities(
                $this->BuildEntityRequest($this->EntityMap->GetProperties()));
        $this->ClearQuery();
        
        return $Entities;
    }

    public function getIterator() {
        return new \ArrayIterator($this->AsArray());
    }
    
    public function First() {
        $this->LimitAmount = 1;
        $Array = $this->AsArray();
        return reset($Array) ?: null;
    }
    
    public function Exists() {
        return $this->EntityManager->LoadExists($this->BuildDataRequest(['E' => O\Expression::Value(1)]));
    }
    
    public function Select(callable $Function) {
        $ReturnExpression = $this->GetReturnExpression($this->GetEntityAndOrAggregateFunctionExpressionTree($Function));
        if(!($ReturnExpression instanceof O\ArrayExpression)) {
            return $this->LoadArrayOfValues($ReturnExpression);
        }
        else {
            $AliasExpressionMap = $this->ParseDataExpression($ReturnExpression);
            
            return $this->EntityManager->LoadData($this->BuildDataRequest($AliasExpressionMap));
        }
    }

    private function ParseDataExpression(O\ArrayExpression $ReturnDataExpression) {        
        $AliasExpressionMap = [];
        
        $KeyExpressions = $ReturnDataExpression->GetKeyExpressions();
        $ValueExpressions = $ReturnDataExpression->GetValueExpressions();
        
        foreach ($KeyExpressions as $Key => $KeyExpression) {
            if(!($KeyExpression instanceof O\ValueExpression)) {
                throw new PinqException(
                        'Return array for data request must contain constant keys.');
            }
            
            $ValueExpression = $ValueExpressions[$Key];
            
            if($KeyExpression !== null) {
                $Alias = $KeyExpression->GetValue();
                $AliasExpressionMap[$Alias] = $ValueExpression;
            }
            else {
                $AliasExpressionMap[] = $ValueExpression;
            }
        }
        
        if(count($AliasExpressionMap) === 0) {
            if(!($KeyExpression instanceof O\ValueExpression)) {
                throw new PinqException(
                        'Return array must contain atleast one key value pair');
            }
        }
        
        return $AliasExpressionMap;
    }
    
    private function LoadArrayOfValues(O\Expression $ValueExpression) {
        $ValueSet = $this->EntityManager->LoadData($this->BuildDataRequest(['E' => $ValueExpression]));
        
        $Values = [];
        foreach($ValueSet as $ValueRow) {
            $Values[] = $ValueRow['E'];
        }
        
        return $Values;
    }
    
    private function LoadAggregateValue(A\AggregateExpression $AggregateExpression) {
        if(count($this->GroupByFunctions) === 0 
                && $this->LimitAmount === null
                && $this->SkipAmount === 0) {
            
            $Values = $this->LoadArrayOfValues($AggregateExpression);
        }
        else {
            $SubEntityRequest = $this->BuildEntityRequest($this->EntityMap->GetProperties());
            $this->ClearQuery();
            $this->SubEntityRequest = $SubEntityRequest;
            $this->LimitAmount = 1;
            
            $Values = $this->LoadArrayOfValues($AggregateExpression);
        }
        
        return reset($Values) ?: null;
    }

    public function Count() {
        return $this->LoadAggregateValue(A\AggregateExpression::Count());
    }
    
    public function All(callable $Function) {
        return $this->LoadAggregateValue(A\AggregateExpression::All(
                $this->GetReturnExpression($this->GetEntityFunctionExpressionTree($Function))));
    }

    public function Any(callable $Functionl) {
        return $this->LoadAggregateValue(A\AggregateExpression::Any(
                $this->GetReturnExpression($this->GetEntityFunctionExpressionTree($Function))));
    }

    public function Average(callable $Function) {
        return $this->LoadAggregateValue(A\AggregateExpression::Average(
                false,
                $this->GetReturnExpression($this->GetEntityFunctionExpressionTree($Function))));
    }

    public function Implode($Delimiter, callable $Function) {
        return $this->LoadAggregateValue(A\AggregateExpression::Implode(
                false,
                $Delimiter,
                $this->GetReturnExpression($this->GetEntityFunctionExpressionTree($Function))));
    }

    public function Maximum(callable $Function) {
        return $this->LoadAggregateValue(A\AggregateExpression::Maximum(
                $this->GetReturnExpression($this->GetEntityFunctionExpressionTree($Function))));
    }

    public function Minimum(callable $Function) {
        return $this->LoadAggregateValue(A\AggregateExpression::Minimum(
                $this->GetReturnExpression($this->GetEntityFunctionExpressionTree($Function))));
    }

    public function Sum(callable $Function) {
        return $this->LoadAggregateValue(A\AggregateExpression::Sum(
                false,
                $this->GetReturnExpression($this->GetEntityFunctionExpressionTree($Function))));
    }
}

?>
