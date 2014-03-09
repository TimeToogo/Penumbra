<?php

namespace Storm\Drivers\Pinq\Object;

trait ReturnExpression {
    
    final public function ParseReturnExpression(Functional\ExpressionTree $ExpressionTree, $Type) {
        if(!$ExpressionTree->HasReturnExpression()) {
            throw FluentException::MustContainValidReturnExpression($Type);
        }
            
        $ReturnExpression = $ExpressionTree->GetReturnExpression();
        if(!$ReturnExpression->HasValueExpression()) {
            throw FluentException::MustContainValidReturnExpression($Type);
        }
        
        return $ReturnExpression->GetValueExpression();
    }
}

?>
