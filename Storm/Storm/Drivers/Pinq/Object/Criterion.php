<?php

namespace Storm\Drivers\Pinq\Object;

use \Storm\Drivers\Base\Object;

class Criterion extends Object\Criterion {
    
    use ReturnExpression;
    
    public function __construct($EntityType) {
        parent::__construct($EntityType);
    }
    
    public function AddPredicateExpression(Functional\ExpressionTree $ExpressionTree) {
        $this->AddPredicate($this->ParseReturnExpression($ExpressionTree, 'predicate'));
    }
    
    public function AddOrderByExpression(Functional\ExpressionTree $ExpressionTree, $Ascending) {
        $this->AddOrderByExpression($this->ParseReturnExpression($ExpressionTree, 'order by'), $Ascending);
    }
}

?>
