<?php

namespace Penumbra\Core\Object\Expressions;

class ClosureExpression extends Expression {
    private $ParameterNames;
    private $UsedVariables;
    private $BodyExpressions;
    
    public function __construct(array $ParameterNames, array $UsedVariables, array $BodyExpressions) {
        $this->ParameterNames = $ParameterNames;
        $this->UsedVariables = $UsedVariables;
        $this->BodyExpressions = $BodyExpressions;
    }

    public function GetParameterNames() {
        return $this->ParameterNames;
    }

    public function GetUsedVariableNames() {
        return $this->UsedVariables;
    }

    /**
     * @return Expression[]
     */
    public function GetBodyExpressions() {
        return $this->BodyExpressions;
    }

    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkClosure($this);
    }
    
    public function Simplify() {
        return $this->Update(
                $this->ParameterNames, 
                $this->UsedVariables, 
                self::SimplifyAll($this->BodyExpressions));
    }
    
    public function Update(array $ParameterNames, array $UsedVariables, array $BodyExpressions) {
        if($this->ParameterNames === $ParameterNames
                && $this->UsedVariables === $UsedVariables
                && $this->BodyExpressions === $BodyExpressions) {
            return $this;
        }
        
        return new self($ParameterNames, $UsedVariables, $BodyExpressions);
    }
}

?>