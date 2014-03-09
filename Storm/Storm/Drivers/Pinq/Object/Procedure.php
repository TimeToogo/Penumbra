<?php

namespace Storm\Drivers\Pinq\Object;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Object\Expressions as O;;

class Procedure extends Object\Procedure {
    public function __construct(
            $EntityType,
            Functional\ExpressionTree $ExpressionTree,
            \Storm\Core\Object\ICriterion $Criterion = null) {
        
        parent::__construct(
                $EntityType, 
                $this->ParseAssignmentExpressions($ExpressionTree), 
                $Criterion ?: new Criterion($EntityType));
    }
    
    private function ParseAssignmentExpressions(Functional\ExpressionTree $ExpressionTree) {
        $Expressions = $ExpressionTree->GetExpressions();
        
        foreach ($Expressions as $Key => $Expression) {
            if($Expression instanceof O\AssignmentExpression
                    && $Expression->GetAssignToExpression() instanceof O\PropertyExpression) {
                continue;
            }
            
            unset($Expressions[$Key]);
        }
        
        return $Expressions;
    }
}

?>
