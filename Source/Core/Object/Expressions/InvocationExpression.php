<?php

namespace Penumbra\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class InvocationExpression extends TraversalExpression {
    private $ArgumentExpressions;
    
    public function __construct(Expression $ValueExpression, array $ArgumentExpressions) {
        parent::__construct($ValueExpression);
        
        $this->ArgumentExpressions = $ArgumentExpressions;
    }
    
    /**
     * @return Expression[]
     */
    public function GetArgumentExpressions() {
        return $this->ArgumentExpressions;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkInvocation($this);
    }
    
    protected function Simplify() {
        $ValueExpression = $this->ValueExpression->Simplify();
        $ArgumentExpressions = self::SimplifyAll($this->ArgumentExpressions);
        
        if($ValueExpression instanceof ValueExpression 
                && self::AllOfType($ArgumentExpressions, ValueExpression::GetType())) {
            $ObjectValue = $ValueExpression->GetValue();
            $ArgumentValues = [];
            foreach($ArgumentExpressions as $ArgumentExpression) {
                $ArgumentValues[] = $ArgumentExpression->GetValue();
            }
            
            return Expression::Value(call_user_func_array([$ObjectValue], $ArgumentValues));
        }
        
        return $this->Update(
                $ValueExpression,
                $ArgumentExpressions);
    }    
    
    /**
     * @return self
     */
    public function Update(Expression $ValueExpression, array $ArgumentExpressions) {
        if($this->ValueExpression === $ValueExpression
                && $this->ArgumentExpressions === $ArgumentExpressions) {
            return $this;
        }
        
        return new self($ValueExpression, $ArgumentExpressions);
    }
    
    protected function UpdateValueExpression(Expression $ValueExpression) {
        return new self($ValueExpression, $this->ArgumentExpressions);
    }
}

?>