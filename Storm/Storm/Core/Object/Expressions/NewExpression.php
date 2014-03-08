<?php

namespace Storm\Core\Object\Expressions;

/**
 * Expression representing the instantiating of a class.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class NewExpression extends Expression {
    private $ClassType;
    private $ArgumentExpressions;
    
    public function __construct($ClassType, array $ArgumentExpressions = []) {
        $this->ClassType = $ClassType;
        $this->ArgumentExpressions = $ArgumentExpressions;
    }
    
    public function GetClassType() {
        return $this->ClassType;
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
                $this->ClassType,
                self::SimplifyAll($this->ArgumentExpressions));
    }
    
    /**
     * @return self
     */
    public function Update($ClassType, array $ArgumentExpressions = []) {
        if($this->ClassType === $ClassType
                && $this->ArgumentExpressions === $ArgumentExpressions) {
            return $this;
        }
        
        return new self($ClassType, $ArgumentExpressions);
    }
}

?>