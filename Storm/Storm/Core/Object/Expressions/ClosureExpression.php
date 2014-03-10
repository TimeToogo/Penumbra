<?php

namespace Storm\Core\Object\Expressions;

class ClosureExpression extends Expression {
    private $ParameterNames;
    private $UsedVariables;
    private $BodyExpressions;
    
    public function __construct(araay $ParameterNames, array $UsedVariables, array $BodyExpressions) {
        $this->ParameterNames = $ParameterNames;
        $this->UsedVariables = $UsedVariables;
        $this->BodyExpressions = $BodyExpressions;
    }
    
    public function GetParameterNames() {
        return $this->ParameterNames;
    }

    public function GetUsedVariables() {
        return $this->UsedVariables;
    }

    public function GetBodyExpressions() {
        return $this->BodyExpressions;
    }

    /**
     * @return Expression[]
     */
    public function GetBodyExpressions() {
        return $this->BodyExpressions;
    }

    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkFunction($this);
    }
    
    public function Simplify() {
        return $this;
    }
}

?>