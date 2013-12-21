<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class MethodSignatureExpression extends Expression {
    private $AccessLevel;
    private $IsStatic;
    private $IsFinal;
    private $IsAbstract;
    private $Name;
    private $ParameterExpressions;
    
    public function __construct($AccessLevel, $IsStatic, $IsFinal, $IsAbstract, 
            $Name, array $ParameterExpressions = array()) {
        $this->AccessLevel = $AccessLevel;
        $this->IsFinal = $IsFinal;
        $this->IsStatic = $IsStatic;
        $this->IsAbstract = $IsAbstract;
        $this->Name = $Name;
        $this->ParameterExpressions = $ParameterExpressions;
    }
    
    public function GetAccessLevel() {
        return $this->AccessLevel;
    }
    
    public function IsStatic() {
        return $this->IsStatic;
    }
    
    public function IsFinal() {
        return $this->IsFinal;
    }
    
    public function IsAbstract() {
        return $this->IsAbstract;
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
        if($this->IsAbstract)
            $Code .= 'abstract ';
        
        if($this->IsFinal)
            $Code .= 'final ';
        
        $Code .= $this->AccessLevel . ' ';
        
        if($this->IsStatic)
            $Code .= 'static ';
        
        $Code .= 'function';
        $Code .= '(' . implode(', ', $this->ParameterExpressions) . ')';
    }

}

?>