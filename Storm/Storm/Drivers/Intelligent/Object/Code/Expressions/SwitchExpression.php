<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class SwitchExpression extends Expression {
    private $SwitchValueExpression;
    private $CaseExpressions;
    private $HasDefaultExpresion;
    private $DefaultExpresion;
    
    public function __construct(ValueExpression $SwitchValueExpression,
            array $CaseExpressions, DefaultExpression $DefaultExpression = null) {
        $this->SwitchValueExpression = $SwitchValueExpression;
        $this->CaseExpressions = $CaseExpressions;
        $this->HasDefaultExpresion = $DefaultExpression !== null;
        $this->DefaultExpresion = $DefaultExpression;
    }
    
    /**
     * @return ValueExpression
     */
    public function getSwitchValueExpression() {
        return $this->SwitchValueExpression;
    }
        
    /**
     * @return CaseExpression[]
     */    
    public function GetCaseExpressions() {
        return $this->CaseExpressions;
    }
   
    public function HasDefaultExpresion() {
        return $this->HasDefaultExpresion;
    }
        
    /**
     * @return DefaultExpression
     */
    public function GetDefaultExpresion() {
        return $this->DefaultExpresion;
    }

    protected function CompileCode(&$Code) {
        $Code .= 'switch';
        $Code .= '(' . $this->SwitchValueExpression->Compile() . ')';
        $Code .= '{';
        
        foreach($this->CaseExpressions as $CaseExpression) {
            $Code .= $CaseExpression->Compile() . ' ';
        }
        
        if($this->HasDefaultExpresion) {
            $Code .= $this->DefaultExpresion->Compile() . ' ';
        }
        
        $Code .= '}';
    }

}

?>