<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions;

class InvocationGetter extends InvocationBase implements IPropertyGetter {
    
     public function ResolveTraversalExpression(TraversalExpression $Expression, PropertyExpression $PropertyExpression) {
        if($Expression instanceof Expressions\InvocationExpression
                || $this->MatchesInvokeMethodCall($Expression)) {
            
            if($this->MatchesContantArguments($Expression->GetArgumentExpressions())) {
                return $PropertyExpression;
            }
        }
    }
    
    public function GetValueFrom($Entity) {
        return $this->Reflection->invokeArgs($Entity, $this->ConstantArguments);
    }
}

?>
