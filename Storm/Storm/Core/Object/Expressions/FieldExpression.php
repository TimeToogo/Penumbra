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
     * @return Expression
     */
    public function GetNameExpression() {
        return $this->Name;
    }
}

?>