<?php

namespace Storm\Drivers\Fluent\Object;

use \Storm\Core\Object\Expressions\ExpressionTree;

trait ReturnExpression {
    
    final public function ParseReturnExpression(ExpressionTree $ExpressionTree, $Type) {
        if(!$ExpressionTree->HasReturnExpression()) {
            throw FluentException::MustContainValidReturnExpression($Type);
        }        
        
        return $ExpressionTree->GetReturnExpression()->GetValueExpression();
    }
}

?>
