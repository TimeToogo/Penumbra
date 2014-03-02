<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions;
use \Storm\Core\Object\Expressions\TraversalExpression;

class InvocationSetter extends InvocationBase implements IPropertySetter {
    
    public function MatchesExpression(TraversalExpression $Expression, &$AssignmentValueExpression = null) {
        if($Expression instanceof Expressions\InvocationExpression) {
            $ArgumentExpressions = $Expression->GetArgumentExpressions();
            $AssignmentValue = array_pop($ArgumentExpressions);
            if($this->Values($ArgumentExpressions) === $this->ConstantArguments) {
                $AssignmentValueExpression = $AssignmentValue;
                return true;
            }
        }
        return false;
    }
    
    public function SetValueTo($Entity, $Value) {
        $this->Reflection->invokeArgs($Entity, array_merge($this->ConstantArguments, [$Value]));
    }
}

?>