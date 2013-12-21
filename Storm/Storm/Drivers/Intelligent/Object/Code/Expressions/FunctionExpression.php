<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;


class FunctionExpression extends BlockBodiedExpression {
    private $Name;
    private $ParameterExpressions;
    
    public function __construct($Name, array $ParameterExpressions, BlockExpression $BodyExpression) {
        parent::__construct($BodyExpression);
        $this->Name = $Name;
        $this->ParameterExpressions = $ParameterExpressions;
    }
    
    public function GetName() {
        return $this->Name;
    }
    
    /**
     * @return ParameterExpression[]
     */
    public function GetParameterExpressions() {
        return $this->ParameterExpressions;
    }

    protected function CompileCode(&$Code) {
        $Code .= 'function ' . $this->Name;
        $Code .= '(' . implode(', ', $this->ParameterExpressions) . ')';
        $Code .= $this->GetBodyExpression()->Compile();
    }
}

?>