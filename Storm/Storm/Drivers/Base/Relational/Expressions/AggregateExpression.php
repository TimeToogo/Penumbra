<?php

 namespace Storm\Drivers\Base\Relational\Expressions;

class AggregateExpression extends FunctionCallExpression {
    public function __construct($Name, ValueListExpression $ArgumentValueListExpression) {
        parent::__construct($Name, $ArgumentValueListExpression);
    }
    
    /**
     * @return self
     */
    public function Update($Name, ValueListExpression $ArgumentValueListExpression) {
        if($this->GetName() === $Name && $this->GetArgumentValueListExpression() === $ArgumentValueListExpression) {
            return $this;
        }
        
        return new self($Name, $ArgumentValueListExpression);
    }
}

?>