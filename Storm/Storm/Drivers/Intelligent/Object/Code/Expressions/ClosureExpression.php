<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class ClosureExpression extends ValueExpression {
    private $IsStatic;
    private $ParameterExpressions;
    private $UsesVariables;
    private $UsedVariableExpressions;
    private $ReturnByReference;
    private $BodyExpression;
    
    public function __construct($IsStatic, array $ParameterExpressions, array $UsedVariableExpressions,
            $ReturnByReference,
            BlockExpression $BodyExpression) {
        $this->IsStatic = $IsStatic;
        $this->ParameterExpressions = $ParameterExpressions;
        $this->UsesVariables = count($UsedVariableExpressions) > 0;
        $this->UsedVariableExpressions = $UsedVariableExpressions;
        $this->ReturnByReference = $ReturnByReference;
        $this->BodyExpression = $BodyExpression;
    }
    
    public function IsStatic() {
        return $this->IsStatic;
    }

    public function ReturnByReference() {
        return $this->ReturnByReference;
    }

    /**
     * @return ParameterExpression[]
     */
    public function GetParameterExpressions() {
        return $this->ParameterExpressions;
    }

    public function UsesVariables() {
        return $this->UsesVariables;
    }
    
    /**
     * @return UsedVariableExpression[]
     */
    public function GetUsedVariableExpressions() {
        return $this->UsedVariableExpressions;
    }
    
    /**
     * @return BlockExpression[]
     */
    public function GetBodyExpression() {
        return $this->BodyExpression;
    }

    protected function CompileCode(&$Code) {
        if($this->IsStatic)
            $Code .= 'static ';
        
        $Code .= 'function ';
        if($this->ReturnByReference)
            $Code .= '& ';
        
        $Code .= '(' . implode(', ', $this->ParameterExpressions) . ') ';
        if($this->UsesVariables) {
            $Code .= 'uses (' . implode(', ', $this->UsedVariableExpressions) . ') ';
        }
        $Code .= $this->BodyExpression->Compile();
    }

}

?>