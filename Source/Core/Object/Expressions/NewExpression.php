<?php

namespace Penumbra\Core\Object\Expressions;

/**
 * Expression representing the instantiating of a class.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class NewExpression extends Expression {
    private $ClassTypeExpression;
    private $ArgumentExpressions;
    
    public function __construct(Expression $ClassTypeExpression, array $ArgumentExpressions = []) {
        $this->ClassTypeExpression = $ClassTypeExpression;
        $this->ArgumentExpressions = $ArgumentExpressions;
    }
    
    /**
     * @return Expression
     */
    public function GetClassTypeExpression() {
        return $this->ClassTypeExpression;
    }
    
    /**
     * @return Expression[]
     */
    public function GetArgumentExpressions() {
        return $this->ArgumentExpressions;
    }
    
    public function Traverse(ExpressionWalker $Walker) {
        return $Walker->WalkNew($this);
    }
    
    public function Simplify() {
        //TODO: white list of deterministic classes to instanstiate
        return $this->Update(
                $this->ClassTypeExpression->Simplify(),
                self::SimplifyAll($this->ArgumentExpressions));
    }
    
    /**
     * @return self
     */
    public function Update(Expression $ClassTypeExpression, array $ArgumentExpressions = []) {
        if($this->ClassTypeExpression === $ClassTypeExpression
                && $this->ArgumentExpressions === $ArgumentExpressions) {
            return $this;
        }
        
        return new self($ClassTypeExpression, $ArgumentExpressions);
    }
}

?>