<?php

namespace Storm\Core\Object\Expressions;

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
    
    /**
     * @return self
     */
    public function Update(Expression $ObjectValueExpression, array $ArgumentExpressions) {
        if($this->GetValueExpression() === $ObjectValueExpression
                && $this->ArgumentExpressions === $ArgumentExpressions) {
            return $this;
        }
        
        return new self($ObjectValueExpression, $ArgumentExpressions);
    }
    
    protected function UpdateValueExpression(Expression $ValueExpression) {
        return new self($ValueExpression, $this->ArgumentExpressions);
    }
}

?>