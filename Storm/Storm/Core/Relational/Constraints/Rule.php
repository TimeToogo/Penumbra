<?php

namespace Storm\Core\Relational\Constraints;

use \Storm\Core\Relational\Expressions\Expression;

class Rule {
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