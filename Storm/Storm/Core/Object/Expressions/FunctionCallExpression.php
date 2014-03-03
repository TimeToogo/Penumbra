<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FunctionCallExpression extends Expression {
    private $Name;
    private $ArgumentExpressions;
    public function __construct($Name, array $ArgumentExpressions = []) {
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
    public function Update($Name, array $ArgumentExpressions = []) {
        if($this->Name === $Name
                && $this->ArgumentExpressions === $ArgumentExpressions) {
            return $this;
        }
        
        return new self($Name, $ArgumentExpressions);
    }
}

?>