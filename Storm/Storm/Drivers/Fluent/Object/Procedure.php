<?php

namespace Storm\Drivers\Fluent\Object;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Object\Expressions\AssignmentExpression;
use \Storm\Drivers\Fluent\Object\Functional;


class Procedure extends Object\Procedure {
    public function __construct(
            Functional\IAST $AST,
            \Storm\Core\Object\ICriterion $Criterion = null) {
        
        $EntityType = $AST->GetEntityMap()->GetEntityType();
        
        parent::__construct(
                $EntityType, 
                $this->ParseAssignmentExpressions($AST), 
                $Criterion ?: new Criterion($EntityType));
    }
    
    private function ParseAssignmentExpressions(Functional\IAST $AST) {
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
