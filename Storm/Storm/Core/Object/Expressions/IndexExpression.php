<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IndexExpression extends TraversalExpression {
    private $Index;
    
    public function __construct(Expression $ValueExpression, $Index) {
        parent::__construct($ValueExpression);
        
        $this->Index = $Index;
    }
    
    /**
     * @return mixed
     */
    public function GetIndex() {
        return $this->Index;
    }
    
    /**
     * @return self
     */
    public function Update(Expression $ObjectValueExpression, $Index) {
        if($this->GetValueExpression() === $ObjectValueExpression
                && $this->Index === $Index) {
            return $this;
        }
        
        return new self($ObjectValueExpression, $Index);
    }
    
    protected function UpdateValueExpression(Expression $ValueExpression) {
        return new self($ValueExpression, $this->Index);
    }
}

?>