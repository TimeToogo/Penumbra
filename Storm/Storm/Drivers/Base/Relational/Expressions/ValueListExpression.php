<?php

namespace Storm\Drivers\Base\Relational\Expressions;
 
use \Storm\Core\Relational\Expression as CoreExpression;

class ValueListExpression extends Expression {
    private $ValueExpressions;
    public function __construct(array $ValueExpressions) {
        $this->ValueExpressions = $ValueExpressions;
    }
    
    /**
     * @return CoreExpression[]
     */
    public function GetValueExpressions() {
        return $this->ValueExpressions;
    }
    
    /**
     * @return self
     */
    public function Update(array $ValueExpressions) {
        if($this->ValueExpressions === $ValueExpressions) {
            return $this;
        }
        
        return new self($ValueExpressions);
    }
}

?>