<?php

namespace Storm\Pinq;

use \Storm\Core\Object\IEntityMap;
use \Storm\Drivers\Base\Object;

class Criteria implements IPredicated, IOrdered, IRanged  {
    /**
     * @var string 
     */
    protected $EntityType;
    
    /**
     * @var IEntityMap 
     */
    protected $EntityMap;
    
    /**
     * @var IFunctionToExpressionTreeConverter 
     */
    protected $FunctionToExpressionTreeConverter;
    
    /**
     * @var Functional\ParameterParser 
     */
    protected $FunctionParameterParser;
    
    protected $WhereFunctions = [];
    protected $OrderByFunctionAscendingTuples = [];
    protected $SkipAmount = 0;
    protected $LimitAmount = null;
    
    public function __construct(IEntityMap $EntityMap, IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        $this->EntityType = $EntityMap->GetEntityType();
        $this->EntityMap = $EntityMap;
        $this->FunctionToExpressionTreeConverter = $FunctionToExpressionTreeConverter;
        $this->FunctionParameterParser = new Functional\ParameterParser();
    }
    
    /**
     * @return static
     */
    public function ClearQuery() {
        $this->WhereFunctions = [];
        $this->OrderByFunctionAscendingTuples = [];
        $this->SkipAmount = 0;
        $this->LimitAmount = null;
        
        return $this;
    }
    
    /**
     * @return static
     */
    public function Where(callable $Function) {
        $this->WhereFunctions[] = $Function;
        return $this;
    }
    
    /**
     * @return static
     */
    public function OrderBy(callable $Function) {
        $this->OrderByFunctionAscendingTuples[] = [$Function, true];
        return $this;
    }

    /**
     * @return static
     */
    public function OrderByDescending(callable $Function) {
        $this->OrderByFunctionAscendingTuples[] = [$Function, false];
        return $this;
    }

    /**
     * @return static
     */
    public function Skip($Amount) {
        $this->SkipAmount = $Amount;
        return $this;
    }

    /**
     * @return static
     */
    public function Limit($Amount) {
        $this->LimitAmount = $Amount;
        return $this;
    }
    
    final protected function BuildCriteria() {
        return new Object\Criteria(
                $this->EntityType, 
                $this->GetPredicateExpressions($this->WhereFunctions), 
                $this->GetOrderByAscendingMap($this->OrderByFunctionAscendingTuples), 
                $this->SkipAmount, 
                $this->LimitAmount);
    }
    
    final protected function ConvertAndResolve(\ReflectionFunctionAbstract $Reflection, array $ParameterNameExpressionMap) {
        return $this->FunctionToExpressionTreeConverter->ConvertAndResolve(
                $Reflection, 
                $this->EntityMap, 
                $ParameterNameExpressionMap);
    }
    
    private function GetPredicateExpressions(array $WhereFunctions) {
        $PredicateExpressions = [];
        foreach ($WhereFunctions as $Key => $WhereFunction) {
            $PredicateExpressions[$Key] = 
                    $this->GetReturnExpression($this->GetEntityFunctionExpressionTree($WhereFunction));
        }
        
        return $PredicateExpressions;
    }    
    
    private function GetOrderByAscendingMap(array $OrderByAscendingTuples) {
        $OrderByAscendingMap = new \SplObjectStorage();
        foreach ($OrderByAscendingTuples as $Tuple) {
            list($OrderByFunction, $Ascending) = $Tuple;
            $OrderByAscendingMap->attach(
                    $this->GetReturnExpression($this->GetEntityAndOrAggregateFunctionExpressionTree($OrderByFunction)), 
                    $Ascending);
        }
        
        return $OrderByAscendingMap;
    }
    
    
    // <editor-fold defaultstate="collapsed" desc="Function parsers">
    
    final protected function GetReturnExpression(Functional\ExpressionTree $ExpressionTree) {
        if(!$ExpressionTree->HasReturnExpression()) {
            throw PinqException::MustContainValidReturnExpression('');
        }
        if(!$ExpressionTree->GetReturnExpression()->HasValueExpression()) {
            throw PinqException::MustContainValidReturnExpression('');
        }
        
        return $ExpressionTree->GetReturnExpression()->GetValueExpression();
    }
    
    final protected function GetEntityFunctionExpressionTree(callable $Function) {
        $Reflection = $this->FunctionToExpressionTreeConverter->GetReflection($Function);
        $EntityParameterName = $this->GetSingleParameterName($Reflection, $this->EntityType);

        return $this->ConvertAndResolve($Reflection, [$EntityParameterName => new Expressions\EntityVariableExpression()]);
    }


    final protected function GetAggregateFunctionExpressionTree(callable $Function) {
        $Reflection = $this->FunctionToExpressionTreeConverter->GetReflection($Function);
        $AggregateParameterName = $this->GetSingleParameterName($Reflection, IAggregate::IAggregateType);

        return $this->ConvertAndResolve($Reflection, [$AggregateParameterName => new Expressions\AggregateVariableExpression()]);
    }


    final protected function GetSingleParameterName(\ReflectionFunctionAbstract $Reflection, $Type) {
        if ($Reflection->getNumberOfParameters() !== 1) {
            throw PinqException::InvalidFunctionSignature($Reflection, [$Type]);
        }

        $TypeHintNameMap = $this->FunctionParameterParser->GetFunctionParameterNames($Reflection, [$Type]);

        if (!isset($TypeHintNameMap[$Type])) {
            throw PinqException::InvalidFunctionSignature($Reflection, [$Type]);
        }

        return $TypeHintNameMap[$this->EntityType];
    }


    final protected function GetEntityAndOrAggregateFunctionExpressionTree(callable $Function) {
        $Reflection = $this->FunctionToExpressionTreeConverter->GetReflection($Function);

        $TypeHints = [$this->EntityType, IAggregate::IAggregateType];
        if ($Reflection->getNumberOfParameters() === 0 || $Reflection->getNumberOfParameters() > 2) {
            throw PinqException::InvalidFunctionSignature($Reflection, [$this->EntityType, IAggregate::IAggregateType]);
        }

        $TypeHintParameterMap = $this->FunctionParameterParser->GetFunctionParameterNames($Reflection, $TypeHints);

        $ParameterExpressions = [];
        if (isset($TypeHintParameterMap[$this->EntityType])) {
            $ParameterExpressions[$TypeHintParameterMap[$this->EntityType]] = new Expressions\EntityVariableExpression();
        }         
        if (isset($TypeHintParameterMap[IAggregate::IAggregateType])) {
            $ParameterExpressions[$TypeHintParameterMap[IAggregate::IAggregateType]] = new Expressions\AggregateVariableExpression();
        }

        return $this->ConvertAndResolve($Reflection, $ParameterExpressions);
    }

    // </editor-fold>
}

?>
