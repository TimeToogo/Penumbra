<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class MethodCallExpression extends ObjectOperationExpression {
    private $Name;
    private $ArgumentExpressions;
    
    public function __construct(Expression $ObjectValueExpression, $Name, array $ArgumentExpressions = []) {
        parent::__construct($ObjectValueExpression);
        
        $this->Name = $Name;
        $this->ArgumentExpressions = $ArgumentExpressions;
    }
    
    /**
     * @return string
     */
    public function GetName() {
        return $this->Name;
    }
    
    /**
     * @return Expression[]
     */
    public function GetArgumentExpressions() {
        return $this->ArgumentExpressions;
    }
    
    /**
     * @return self
     */
    public function Update(Expression $ObjectValueExpression, $Name, array $ArgumentExpressions) {
        if($this->GetValueExpression() === $ObjectValueExpression
                && $this->Name === $Name
                && $this->ArgumentExpressions === $ArgumentExpressions) {
            return $this;
        }
        
        return new self($ObjectValueExpression, $Name, $ArgumentExpressions);
    }
    
    protected function UpdateValueExpression(Expression $ValueExpression) {
        return new self($ValueExpression, $this->Name, $this->ArgumentExpressions);
    }
}

?>