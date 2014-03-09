<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FunctionCallExpression extends Expression {
    private $NameExpression;
    private $ArgumentExpressions;
    public function __construct(Expression $NameExpression, array $ArgumentExpressions = []) {
        $this->NameExpression = $NameExpression;
        $this->ArgumentExpressions = $ArgumentExpressions;
    }
    
    /**
     * @return Expression
     */
    public function GetNameExpression() {
        return $this->NameExpression;
    }
    
    /**
     * @return Expression[]
     */
    public function GetArgumentExpressions() {
        return $this->ArgumentExpressions;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkFunctionCall($this);
    }
    
    public function Simplify() {
        //TODO: Add a whitelist of deteministic and side-effect free function.
        return $this->Update(
                $this->NameExpression,
                self::SimplifyAll($this->ArgumentExpressions));
    }
    
    /**
     * @return self
     */
    public function Update(Expression $NameExpression, array $ArgumentExpressions = []) {
        if($this->NameExpression === $NameExpression
                && $this->ArgumentExpressions === $ArgumentExpressions) {
            return $this;
        }
        
        return new self($NameExpression, $ArgumentExpressions);
    }
}

?>