<?php

namespace Storm\Pinq;

use \Storm\Core\Object\IEntityMap;
use \Storm\Drivers\Base\Object;
use \Storm\Core\Object\Expressions as O;

class Procedure extends Object\Procedure {
    
    use FunctionParsing;
    
    public function __construct(
            IEntityMap $EntityMap, 
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter,
            callable $Function,
            \Storm\Core\Object\ICriteria $Criteria = null) {
        $this->EntityMap = $EntityMap;
        $this->FunctionToExpressionTreeConverter = $FunctionToExpressionTreeConverter;
        
        $ProcedureExpressionTree = $this->ParseFunction($Function, [0 => O\Expression::Entity()]);
        
        parent::__construct(
                $EntityMap->GetEntityType(), 
                $this->ParseAssignmentExpressions($ProcedureExpressionTree), 
                $Criteria ?: new Criteria($EntityMap, $FunctionToExpressionTreeConverter));
    }
    
    final protected function ParseAssignmentExpressions(Functional\ExpressionTree $ExpressionTree) {
        $Expressions = $ExpressionTree->GetExpressions();
        
        $PropertyAssignmentExpressions = [];
        foreach ($Expressions as $Key => $Expression) {
            if($Expression instanceof O\AssignmentExpression
                    && $Expression->GetAssignToExpression() instanceof O\PropertyExpression) {
                $PropertyAssignmentExpressions[$Key] = $Expressions[$Key];
            }
            
        }
        
        return $PropertyAssignmentExpressions;
    }
}

?>
