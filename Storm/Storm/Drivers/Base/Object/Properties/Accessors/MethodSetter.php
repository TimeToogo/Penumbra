<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\TraversalExpression;
use \Storm\Core\Object\Expressions\PropertyExpression;

class MethodSetter extends MethodBase implements IPropertySetter {
    
    public function ParseTraversalExpression(TraversalExpression $Expression, PropertyExpression $PropertyExpression) {
        if($Expression instanceof Expressions\MethodCallExpression
                && $Expression->GetName() === $this->MethodName) {
            $ArgumentExpressions = $Expression->GetArgumentExpressions();
            $AssignmentValue = array_pop($ArgumentExpressions);
            
            if($this->MatchesContantArguments($ArgumentExpressions)) {
                return Expression::Assign(
                        $PropertyExpression, 
                        Expressions\Operators\Assignment::Equal, 
                        $AssignmentValue);
            }
        }
    }
    
    public function SetValueTo($Entity, $Value) {
        $this->Reflection->invokeArgs($Entity, array_merge($this->ConstantArguments, [$Value]));
    }
}

?>