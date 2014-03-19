<?php

namespace Storm\Drivers\Platforms\Base\Mapping;

use Storm\Drivers\Base\Mapping\Expressions;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

abstract class OperationMapper implements Expressions\IOperationMapper {
    private $MatchingBinaryOperators;
    private $MatchingUnaryOperators;
    private $MatchingCastTypes;
    
    public function __construct() {
        $this->MatchingBinaryOperators = $this->MatchingBinaryOperators();
        $this->MatchingUnaryOperators = $this->MatchingUnaryOperators();
        $this->MatchingCastTypes = $this->MatchingCastTypes();
    }
    
    protected abstract function MatchingBinaryOperators();
    
    protected abstract function MatchingUnaryOperators();
    
    protected abstract function MatchingCastTypes();
    
    /**
     * @return R\Expression
     */
    final public function MapBinary(
            R\Expression $MappedLeftOperandExpression,
            $Operator,
            R\Expression $MappedRightOperandExpression) {
        if(isset($this->MatchingBinaryOperators[$Operator])) {
            return R\Expression::BinaryOperation(
                    $MappedLeftOperandExpression, 
                    $this->MatchingBinaryOperators[$Operator], 
                    $MappedRightOperandExpression);
        }
        
        $Expression = $this->MapBinaryOperation(
                $MappedLeftOperandExpression,
                $Operator, 
                $MappedRightOperandExpression);
        
        if($Expression === null) {
            throw new \Storm\Core\Mapping\MappingException(
                    '%s could not map binary operator %s: operator in not supported',
                    get_class($this),
                    $Operator);
        }
        
        return $Expression;
    }
    protected abstract function MapBinaryOperation (
            R\Expression $LeftOperand,
            $Operator,
            R\Expression $RightOperand);
        
    /**
     * @return R\Expression
     */
    final public function MapUnary($Operator, R\Expression $MappedOperandExpression) {
        if(isset($this->MatchingUnaryOperators[$Operator])) {
            return R\Expression::BinaryOperation(
                    $this->MatchingUnaryOperators[$Operator], 
                    $MappedOperandExpression);
        }
        
        $Expression = $this->MapUnaryOperation(
                $Operator, 
                $MappedOperandExpression);
        
        if($Expression === null) {
            throw new \Storm\Core\Mapping\MappingException(
                    '%s could not map unary operator %s: operator in not supported',
                    get_class($this),
                    $Operator);
        }
        
        return $Expression;
    }
    protected abstract function MapUnaryOperation (
            $Operator,
            R\Expression $Operand);
    
    final public function MapCast($CastType, R\Expression $MappedCastValueExpression) {
        if(isset($this->MatchingCastTypes[$CastType])) {
            return $this->CastAsTypeExpression($CastType, $MappedCastValueExpression);
        }
        
        $Expression = $this->MapCast($CastType, $MappedCastValueExpression);
        
        if($Expression === null) {
            throw new \Storm\Core\Mapping\MappingException(
                    '%s could not map cast operator %s: operator in not supported',
                    get_class($this),
                    $CastType);
        }
        
        return $Expression;
    }
    protected abstract function MapCastOperation($CastType, R\Expression $ValueExpression);
    
    protected abstract function CastAsTypeExpression($Type, R\Expression $ValueExpression);
}

?>