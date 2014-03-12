<?php

namespace Storm\Pinq;

use \Storm\Core\Object\IEntityMap;

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
    
    private $WhereFunctions = [];
    private $OrderByFunctionAscendingTuples = [];
    private $SkipAmount = 0;
    private $LimitAmount = null;
    
    public function __construct(IEntityMap $EntityMap, IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        $this->EntityType = $EntityMap->GetEntityType();
        $this->EntityMap = $EntityMap;
        $this->FunctionToExpressionTreeConverter = $FunctionToExpressionTreeConverter;
    }
    
    public function Where(callable $Function) {
        $this->WhereFunctions[] = $Function;
    }
    
    public function OrderBy(callable $Function) {
        $this->OrderByFunctionAscendingTuples[] = [$Function, true];
    }

    public function OrderByDescending(callable $Function) {
        $this->OrderByFunctionAscendingTuples[] = [$Function, false];
    }

    public function Skip($Amount) {
        $this->SkipAmount = $Amount;
    }

    public function Limit($Amount) {
        $this->LimitAmount = $Amount;
    }
    
    final protected function BuildCriteria() {
        return new \Storm\Drivers\Base\Object\Criteria(
                $this->EntityType, 
                $PredicateExpressions, 
                $OrderByExpressionsAscendingMap, 
                $GroupByExpressions, 
                $RangeOffset, 
                $RangeAmount);
    }
    
    final protected function ConvertAndResolve(\ReflectionFunctionAbstract $Reflection, array $ParameterNameExpressionMap) {
        return $this->FunctionToExpressionTreeConverter->ConvertAndResolve(
                $Reflection, 
                $this->EntityMap, 
                $ParameterNameExpressionMap);
    }
    
    private function ParseWhereFunction(callable $Function) {
        $Reflection = $this->FunctionToExpressionTreeConverter->GetReflection($Function);
        
    }
    
    final protected function ParseAsEntityParameter(\ReflectionFunctionAbstract $Reflection) {
        return $this->ParseSingleParameter($Reflection, $this->EntityType);
    }
    
    final protected function ParseAsAggregateParameter(\ReflectionFunctionAbstract $Reflection) {
        return $this->ParseSingleParameter($Reflection, IAggregate::IAggregateType);
    }
    
    final protected function ParseEntityAndOrAggregateParameter(\ReflectionFunctionAbstract $Reflection) {
        $TypeHints = [$this->EntityType, IAggregate::IAggregateType];
        $Parameters = $Reflection->getParameters();
        if(count($Parameters) !== 2) {
            throw PinqException::InvalidFunctionSignature($Reflection, $TypeHints);
        }
        
        
    }
    
    final protected function ParseFunctionSignature(\ReflectionFunctionAbstract $Reflection, array $TypeHints) {
        $Parameters = $Reflection->getParameters();
        
        if(count($Parameters) !== count($TypeHints)) {
            throw PinqException::InvalidFunctionSignature($Reflection, $TypeHints);
        }
        
        $TypeHints = array_flip(array_values($TypeHints));
        $TypeHintNameMap = [];
        
        foreach($Parameters as $Parameter) {
            $Class = $Parameter->getClass();
            if($Class === null || !isset($TypeHints[$Class->name])) {
                throw PinqException::InvalidFunctionSignature($Reflection, $TypeHints);
            }
            $TypeHintNameMap[$Class->name] = $Parameter->name;
            unset($TypeHints[$Class->name]);
        }
        
        return $TypeHintNameMap;
    }
    
    private function ParseSingleParameter(\ReflectionFunctionAbstract $Reflection, $TypeHint) {
        $Parameters = $Reflection->getParameters();
        if(count($Parameters) !== 1) {
            throw PinqException::InvalidFunctionSignature($Reflection, [$TypeHint]);
        }
        $Class = $Parameters[0]->getClass();
        if($Class === null || $Class->name !== $TypeHint) {
            throw PinqException::InvalidFunctionSignature($Reflection, [$TypeHint]);
        }
        
        return $Parameters[0]->name;
    }
}

?>
