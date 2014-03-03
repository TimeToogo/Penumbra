<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FieldExpression extends ObjectOperationExpression {
    private $Name;
    
    public function __construct(Expression $ObjectValueExpression, $Name) {
        parent::__construct($ObjectValueExpression);
        
        $this->Name = $Name;
    }
    
    /**
     * @return string
     */
    public function GetName() {
        return $this->Name;
    }
    
    /**
     * @return self
     */
    public function Update(Expression $ObjectValueExpression, $Name) {
        if($this->GetValueExpression() === $ObjectValueExpression
                && $this->Name === $Name) {
            return $this;
        }
        
        return new self($ObjectValueExpression, $Name);
    }
    
    protected function UpdateValueExpression(Expression $ValueExpression) {
        return new self($ValueExpression, $this->Name);
    }
}

?>