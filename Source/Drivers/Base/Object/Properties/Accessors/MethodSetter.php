<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

use \Penumbra\Core\Object\Expressions;
use \Penumbra\Core\Object\Expressions\Expression;
use \Penumbra\Core\Object\Expressions\TraversalExpression;
use \Penumbra\Core\Object\Expressions\PropertyExpression;

class MethodSetter extends MethodBase implements IPropertySetter {
    
    public function ResolveTraversalExpression(array $TraversalExpressions, PropertyExpression $PropertyExpression) {
        $Expression = $TraversalExpressions[0];
        if($Expression instanceof Expressions\MethodCallExpression
                && $this->MatchesName($Expression->GetNameExpression())) {
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