<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions;
use \Storm\Core\Object\Expressions\TraversalExpression;
use \Storm\Core\Object\Expressions\PropertyExpression;

class MethodGetter extends MethodBase implements IPropertyGetter {
    
    public function ResolveTraversalExpression(TraversalExpression $Expression, PropertyExpression $PropertyExpression) {
        if($Expression instanceof Expressions\MethodCallExpression
                && $this->MatchesName($Expression->GetNameExpression())
                && $this->MatchesContantArguments($Expression->GetArgumentExpressions())) {
            return $PropertyExpression;
        }
    }
    
    public function GetValueFrom($Entity) {
        return $this->Reflection->invoke($Entity, $this->ConstantArguments);
    }
}

?>
