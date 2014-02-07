<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class PropertyFetchExpression extends ObjectOperationExpression {
    private $Name;
    
    public function __construct(Expression $ObjectOrNewExpression, $Name) {
        parent::__construct($ObjectOrNewExpression);
        
        $this->Name = $Name;
    }
    
    /**
     * @return string
     */
    public function GetName() {
        return $this->Name;
    }
}

?>