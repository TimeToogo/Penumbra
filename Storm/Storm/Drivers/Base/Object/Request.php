<?php

namespace Storm\Drivers\Base\Object;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;

abstract class Request implements Object\IRequest {
    private $EntityType;
    
    private $GroupByExpressions = [];
    private $AggregatePredicateExpressions = [];
    
    /**
     * @var Object\ICriteria 
     */
    private $Criteria;
    
    /**
     * @var Object\IEntityRequest|null
     */
    private $SubEntityRequest;
    
    
    public function __construct(
            $EntityOrType, 
            array $GroupByExpressions,
            array $AggregatePredicateExpressions,
            Object\ICriteria $Criteria = null,
            Object\IEntityRequest $SubEntityRequest = null) {
        
        if(is_object($EntityOrType)) {
            $EntityOrType = get_class($EntityOrType);
        }
        $this->EntityType = $EntityOrType;
        
        
        foreach($GroupByExpressions as $GroupByExpression) {
            $this->AddGroupByExpression($GroupByExpression);
        }
        foreach($AggregatePredicateExpressions as $AggregatePredicateExpression) {
            $this->AddAggregatePredicateExpression($AggregatePredicateExpression);
        }
        
        $this->Criteria = $Criteria ?: new Criteria($this->EntityType);
        if($this->Criteria->GetEntityType() !== $this->EntityType) {
            throw new Object\TypeMismatchException(
                    'The supplied criteria must be for %s, %s given',
                    $this->EntityType,
                    $this->Criteria->GetEntityType());
        }
        
        $this->SubEntityRequest = $SubEntityRequest;
    }

    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    public function HasSubEntityRequest() {
        return $this->SubEntityRequest !== null;
    }
    
    public function GetSubEntityRequest() {
        return $this->SubEntityRequest;
    }
        
    final public function GetCriteria() {
        return $this->Criteria;
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
}

?>