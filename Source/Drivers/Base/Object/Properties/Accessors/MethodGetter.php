<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

use \Penumbra\Core\Object\Expressions;
use \Penumbra\Core\Object\Expressions\TraversalExpression;
use \Penumbra\Core\Object\Expressions\PropertyExpression;

class MethodGetter extends MethodBase implements IPropertyGetter {
    
    public function ResolveTraversalExpression(array $TraversalExpressions, PropertyExpression $PropertyExpression) {
        $Expression = $TraversalExpressions[0];
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
