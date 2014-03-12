<?php

namespace Storm\Pinq\Functional\Walkers;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions as O;
use \Storm\Pinq\Expressions\EntityVariableExpression;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class EntityTraversalResolverWalker extends O\ExpressionWalker {
    /**
     * @var Object\IEntityMap
     */
    private $EntityMap; 
    
    public function SetEntityMap(Object\IEntityMap $EntityMap) {
        $this->EntityMap = $EntityMap;
    }

    public function WalkField(O\FieldExpression $Expression) {
        return $this->WalkTraversal($Expression) ?: parent::WalkField($Expression);
    }
    
    public function WalkMethodCall(O\MethodCallExpression $Expression) {
        return $this->WalkTraversal($Expression) ?: parent::WalkMethodCall($Expression);
    }
    
    public function WalkIndex(O\IndexExpression $Expression) {
        return $this->WalkTraversal($Expression) ?: parent::WalkIndex($Expression);
    }
    
    public function WalkInvocation(O\InvocationExpression $Expression) {
        return $this->WalkTraversal($Expression) ?: parent::WalkInvocation($Expression);
    }
    
    private function WalkTraversal(O\TraversalExpression $Expression) {
        if($Expression->OriginatesFrom(EntityVariableExpression::GetType())) {
            return $this->EntityMap->ResolveTraversalExpression($Expression);
        }
        
        return null;
    }
}

?>