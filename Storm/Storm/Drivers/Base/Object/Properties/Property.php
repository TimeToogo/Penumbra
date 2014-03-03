<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;
use \Storm\Core\Object\IProperty;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\PropertyExpression;
use \Storm\Core\Object\Expressions\TraversalExpression;

abstract class Property implements IProperty {
    private $Identifier;
    /**
     * @var Accessors\Accessor 
     */
    protected $Accessor;
    
    /**
     * @var string 
     */
    private $EntityType;
    
    public function __construct(Accessors\Accessor $Accessor) {
        $this->Identifier = $Accessor->GetIdentifier();
        $this->Accessor = $Accessor;
    }
    
    final public function GetIdentifier() {
        return $this->Identifier;
    }
    
    /**
     * @return Accessors\Accessor
     */
    final public function GetAccessor() {
        return $this->Accessor;
    }
    
    final public function ResolveTraversalExpression(TraversalExpression $Expression, Expressions\PropertyExpression $ParentPropertyExpression = null) {
        $OriginalTraversalExpression = $Expression;
        $ResolvedExpression = $ParentPropertyExpression ?: Expressions\Expression::Property($this);
        $ExcessDepth = 0;
        while (!($Expression instanceof Expressions\EntityExpression)) {
            /**
             * Accessors are allowed to return and type of expression, but if there is still
             * excess traversal, it must return the property expression.
             * This is ok as it should only return other than the property expression if it
             * is a setter (e.g. assignment expression) and no traversal should occur after
             * a setter.
             */
            $ResolvedExpression = $this->Accessor->ResolveTraversalExpression($Expression, $ResolvedExpression);
            if($ResolvedExpression !== null) {
                if($ExcessDepth === 0) {
                    return $ResolvedExpression;
                }
                else {
                    return $this->ResolveExcessTraversal($ResolvedExpression, $ExcessDepth, $OriginalTraversalExpression);
                }
            }
            $ExcessDepth++;
            $Expression = $Expression->GetValueExpression();
        }
    }
    
    protected function ResolveExcessTraversal(PropertyExpression $ResolvedExpression, $ExcessDepth, TraversalExpression $ExcessTraversalExpression) {
        if($ExcessDepth === 0) {
            return $ExcessTraversalExpression->UpdateValue($ResolvedExpression);
        }
        
        return $ExcessTraversalExpression->UpdateValue(
                $this->ResolveExcessTraversal($ResolvedExpression, --$ExcessDepth, $ExcessTraversalExpression->GetValueExpression()));
    }


    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function SetEntityType($EntityType) {
        $this->EntityType = $EntityType;
        $this->Accessor->SetEntityType($EntityType);
    }
}

?>
