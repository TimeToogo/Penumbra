<?php

namespace Storm\Drivers\Base\Relational\Expressions;
 
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;

class ValueListExpression extends Expression {
    private $ValueExpressions;
    public function __construct(array $ValueExpressions) {
        $this->ValueExpressions = $ValueExpressions;
    }
    
    /**
     * @return CoreExpression[]
     */
    public function GetValueExpressions() {
        return $this->ValueExpressions;
    }
}

?>