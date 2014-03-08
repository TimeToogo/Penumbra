<?php

namespace Storm\Drivers\Fluent\Object;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Object\Expressions\ExpressionTree;

class Criterion extends Object\Criterion {
    
    use ReturnExpression;
    
    public function __construct($EntityType) {
        parent::__construct($EntityType);
    }
    
    public function AddPredicateExpression(ExpressionTree $ExpressionTree) {
        $this->AddPredicate($this->ParseReturnExpression($ExpressionTree, 'predicate'));
    }
    
    public function AddOrderByExpression(ExpressionTree $ExpressionTree, $Ascending) {
        $this->AddOrderByExpression($this->ParseReturnExpression($ExpressionTree, 'order by'), $Ascending);
    }
}

?>
