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
}

?>