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
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkMethodCall($this);
    }
    
    public function Simplify() {
        $ValueExpression = $this->ValueExpression->Simplify();
        $ArgumentExpressions = self::SimplifyAll($this->ArgumentExpressions);
        
        if($ValueExpression instanceof ValueExpression 
                && self::AllOfType($ArgumentExpressions, ValueExpression::GetType())) {
            $ObjectValue = $ValueExpression->GetValue();
            $ArgumentValues = [];
            foreach($ArgumentExpressions as $ArgumentExpression) {
                $ArgumentValues[] = $ArgumentExpression->GetValue();
            }
            
            return Expression::Value(call_user_func_array([$ObjectValue, $this->Name], $ArgumentValues));
        }
        
        return $this->Update(
                $ValueExpression,
                $this->Name,
                $ArgumentExpressions);
    }
    
    /**
     * @return self
     */
    public function Update(Expression $ObjectValueExpression, $Name, array $ArgumentExpressions) {
        if($this->ValueExpression === $ObjectValueExpression
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