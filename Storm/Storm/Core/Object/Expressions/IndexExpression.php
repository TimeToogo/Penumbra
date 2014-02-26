<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class IndexExpression extends ObjectOperationExpression {
    private $Index;
    
    public function __construct(Expression $ObjectOrNewExpression, $Index) {
        parent::__construct($ObjectOrNewExpression);
        
        $this->Index = $Index;
    }
    
    /**
     * @return string
     */
    public function GetIndex() {
        return $this->Index;
    }
}

?>