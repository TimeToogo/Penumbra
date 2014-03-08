<?php

namespace Storm\Drivers\Fluent\Object;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Object\Expressions\ExpressionTree;
use \Storm\Core\Object\Expressions\AssignmentExpression;

class Procedure extends Object\Procedure {
    public function __construct(
            $EntityType,
            ExpressionTree $ExpressionTree,
            \Storm\Core\Object\ICriterion $Criterion = null) {
        
        parent::__construct(
                $EntityType, 
                $this->ParseAssignmentExpressions($ExpressionTree), 
                $Criterion ?: new Criterion($EntityType));
    }
    
    private function ParseAssignmentExpressions(ExpressionTree $ExpressionTree) {
        $Expressions = $ExpressionTree->GetExpressions();
        
        foreach ($Expressions as $Key => $Expression) {
            if(!($Expression instanceof AssignmentExpression)) {
                unset($Expressions[$Key]);
            }
        }
        
        return $Expressions;
    }
}

?>
