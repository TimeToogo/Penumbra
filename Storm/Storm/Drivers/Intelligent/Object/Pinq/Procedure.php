<?php

namespace Storm\Drivers\Intelligent\Object\Pinq;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Object\Expressions\AssignmentExpression;
use \Storm\Drivers\Intelligent\Object\Closure;


class Procedure extends Object\Procedure {
    public function __construct(
            Closure\IAST $AST,
            \Storm\Core\Object\ICriterion $Criterion = null) {
        
        $EntityType = $AST->GetEntityMap()->GetEntityType();
        
        parent::__construct(
                $EntityType->GetEntityType(), 
                $this->ParseAssignmentExpressions($AST), 
                $Criterion ?: new Criterion($EntityType));
    }  
    
    private function ParseAssignmentExpressions(Closure\IAST $AST) {
        $Expressions = $AST->ParseNodes();
        
        foreach ($Expressions as $Key => $Expression) {
            if(!($Expression instanceof AssignmentExpression)) {
                unset($Expressions[$Key]);
            }
        }
        
        return $Expressions;
    }
}

?>
