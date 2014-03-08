<?php

namespace Storm\Drivers\Base\Object;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;

class Request implements Object\IRequest {
    private $EntityType;
    private $Properties = [];
    private $GroupByExpressions = [];
    private $AggregatePredicateExpressions = [];
    private $IsSingleEntity;
    
    /**
     * @var Object\ICriterion 
     */
    private $Criterion;
    
    public function __construct(
            $EntityOrType, 
            array $Properties, 
            array $GroupByExpressions,
            array $AggregatePredicateExpressions,
            $IsSingleEntity, 
            Object\ICriterion $Criterion = null) {
        
        if(is_object($EntityOrType)) {
            $EntityOrType = get_class($EntityOrType);
        }
        $this->EntityType = $EntityOrType;
        foreach($Properties as $Property) {
            $this->AddProperty($Property);
        }
        
        foreach($GroupByExpressions as $GroupByExpression) {
            $this->AddGroupByExpression($GroupByExpression);
        }
        foreach($AggregatePredicateExpressions as $AggregatePredicateExpression) {
            $this->AddAggregatePredicateExpression($AggregatePredicateExpression);
        }
        
        $this->IsSingleEntity = $IsSingleEntity;
        $this->Criterion = $Criterion ?: new Criterion($this->EntityType);
        if($this->Criterion->GetEntityType() !== $this->EntityType) {
            throw new Object\TypeMismatchException(
                    'The supplied criterion must be for %s, %s given',
                    $this->EntityType,
                    $this->Criterion->GetEntityType());
        }
    }
    
    final protected function AddProperty(Object\IProperty $Property) {
        $this->Properties[$Property->GetIdentifier()] = $Property;
    }
    
    final protected function AddGroupByExpression(Expression $GroupByExpression) {
        $this->GroupByExpressions[] = $GroupByExpression;
    }
    
    final protected function AddAggregatePredicateExpression(Expression $AggregatePredicateExpression) {
        if(count($this->GroupByExpressions) === 0) {
            throw new Object\ObjectException(
                    'Cannot have any aggregate predicate expression when no group by expressions are specified');
        }
        $this->AggregatePredicateExpressions[] = $AggregatePredicateExpression;
    }
    
    final public function IsSingleEntity() {
        return $this->IsSingleEntity;
    }

    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function GetProperties() {
        return $this->Properties;
    }
    
    final public function GetCriterion() {
        return $this->Criterion;
    }

    final public function IsGrouped() {
        return count($this->GroupByExpressions) > 0;
    }

    final public function GetGroupByExpressions() {
        return $this->GroupByExpressions;
    }

    final public function IsAggregateConstrained() {
        return count($this->AggregatePredicateExpressions) > 0;
    }

    final public function GetAggregatePredicateExpressions() {
        return $this->AggregatePredicateExpressions;
    }

}

?>