<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class StaticPropertyExpression extends ValueExpression {
    private $ClassTypeValueExpression;
    private $PropertyNameValueExpression;
    
    public function __construct(ValueExpression $ClassTypeValueExpression, ValueExpression $PropertyNameValueExpression) {
        $this->ClassTypeValueExpression = $ClassTypeValueExpression;
        $this->PropertyNameValueExpression = $PropertyNameValueExpression;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetClassTypeValueExpression() {
        return $this->ClassTypeValueExpression;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetPropertyNameValueExpression() {
        return $this->PropertyNameValueExpression;
    }    
    
    protected function CompileCode(&$Code) {
        $Code .= $this->ClassTypeValueExpression->Compile() . '::${' .  
                $this->PropertyNameValueExpression->Compile() . '}';
    }
}

?>