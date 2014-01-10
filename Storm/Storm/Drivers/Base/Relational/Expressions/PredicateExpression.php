<?php

namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Drivers\Base\Relational\Traits\ForeignKey;
use \Storm\Drivers\Base\Relational\Expressions\Operators\Binary;

class PredicateExpression extends BinaryOperationExpression {
    
    public function __construct(array $Expressions, $LogicalOperator = Binary::LogicalAnd) {
       
        $PredicateExpression = null;
        
        foreach($Expressions as $Expression) {
            if($PredicateExpression === null) {
                $PredicateExpression = $Expression;
            }
            else {
                $PredicateExpression = Expression::BinaryOperation(
                        $PredicateExpression, 
                        $LogicalOperator,
                        $Expression);
            }
        }
        
        parent::__construct(
                $PredicateExpression->GetLeftOperandExpression(), 
                $PredicateExpression->GetOperator(), 
                $PredicateExpression->GetRightOperandExpression());
    }
}

?>