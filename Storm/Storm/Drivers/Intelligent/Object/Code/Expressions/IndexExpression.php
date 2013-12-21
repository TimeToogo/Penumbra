<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class IndexExpression extends ValueExpression {
    private $ValueExpression;
    private $IndexValueExpression;
    public function __construct(ValueExpression $ValueExpression, ValueExpression $IndexValueExpression) {
        parent::__construct();
        
        $this->ValueExpression = $ValueExpression;
        $this->IndexValueExpression = $IndexValueExpression;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetValueExpression() {
        return $this->ValueExpression;
    }
    
    /**
     * @return ValueExpression
     */
    public function GetIndexValueExpression() {
        return $this->IndexValueExpression;
    }
    
    protected function CompileCode(&$Code) {
        $Code .= $this->ValueExpression->Compile();
        $Code .= '[' . $this->IndexValueExpression . ']';
    }
}

?>