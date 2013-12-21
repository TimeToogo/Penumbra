<?php

namespace Storm\Core\Object\Constraints;

use \Storm\Core\Object\Expressions\Expression;

class Rule implements IRule {
    private $Expression;
    
    public function __construct(Expression $Expression) {
        $this->Expression = $Expression;
    }
    
    /**
     * @return Expression
     */
    final public function GetExpression() {
        return $this->Expression;
    }
}

?>