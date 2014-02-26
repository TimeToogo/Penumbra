<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class InvocationExpression extends ObjectOperationExpression {
    private $Arguments;
    
    public function __construct(Expression $ObjectOrNewExpression, array $Arguments) {
        parent::__construct($ObjectOrNewExpression);
        
        $this->Arguments = $Arguments;
    }
    
    /**
     * @return array
     */
    public function GetArguments() {
        return $this->Arguments;
    }
}

?>