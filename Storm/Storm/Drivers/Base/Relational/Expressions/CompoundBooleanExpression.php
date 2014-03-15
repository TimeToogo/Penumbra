<?php

namespace Storm\Drivers\Base\Relational\Expressions;


/**
 * Used to represent many boolean expressions.
 * This prevents having to use nested binary operation expression
 * which result in queries with excessive parentheses.
 */
class CompoundBooleanExpression extends Expression {
    private $LogicalOperator;
    private $BooleanExpressions;
    
    public function __construct(array $BooleanExpressions, $LogicalOperator = Operators\Binary::LogicalAnd) {
        if($LogicalOperator !== Operators\Binary::LogicalAnd && $LogicalOperator !== Operators\Binary::LogicalOr) {
            throw new \Storm\Core\UnexpectedValueException(
                    'The supplied operator must be the logical and/or, %s given',
                    \Storm\Core\Utilities::GetTypeOrClass($LogicalOperator));
        }
        $this->LogicalOperator = $LogicalOperator;
        $this->BooleanExpressions = $BooleanExpressions;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkCompundBoolean($this);
    }
    
    public function GetLogicalOperator() {
        return $this->LogicalOperator;
    }

    public function GetBooleanExpressions() {
        return $this->BooleanExpressions;
    }
    
    /**
     * @return self
     */
    public function Update(array $BooleanExpressions, $LogicalOperator = Operators\Binary::LogicalAnd) {
        if($this->BooleanExpressions === $BooleanExpressions && $this->LogicalOperator === $LogicalOperator) {
            return $this;
        }
        
        return new self($BooleanExpressions, $LogicalOperator);
    }
}

?>