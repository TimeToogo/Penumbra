<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class MemberConstantExpression extends ValueExpression {
    private $ClassTypeValueExpression;
    private $ConstantName;
    
    public function __construct(ValueExpression $ClassTypeValueExpression, $ConstantName) {
        $this->ClassTypeValueExpression = $ClassTypeValueExpression;
        $this->ConstantName = $ConstantName;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetClassTypeValueExpression() {
        return $this->ClassTypeValueExpression;
    }
    public function GetConstantName() {
        return $this->ConstantName;
    }    
    
    protected function CompileCode(&$Code) {
        $Code .= $this->ClassTypeValueExpression->Compile() . '::' . $this->ConstantName;
    }
}

?>