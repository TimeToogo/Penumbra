<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions as O;

abstract class Property extends PropertyBase implements Object\IProperty {
    
    /**
     * @var Accessors\Accessor 
     */
    protected $Accessor;
    
    public function __construct(Accessors\Accessor $Accessor) {
        parent::__construct($Accessor->GetIdentifier());
        $this->Accessor = $Accessor;
    }
    
    /**
     * @return Accessors\Accessor
     */
    final public function GetAccessor() {
        return $this->Accessor;
    }
    
    protected function OnSetEntityType($EntityType) {
        $this->Accessor->SetEntityType($EntityType);
    }
    
    final public function ResolveTraversalExpression(O\TraversalExpression $TraversalExpression) {
        $ParentPropertyExpression = 
                $TraversalExpression->OriginatesFrom(O\PropertyExpression::GetType()) ?
                        $TraversalExpression->GetOriginExpression() : null;
        
        $PropertyExpression = O\Expression::Property($this, $ParentPropertyExpression);
        
        $TraversalExpressionArray = [];
        $Expression = $TraversalExpression;
        while ($Expression instanceof O\TraversalExpression) {
            $TraversalExpressionArray[] = $Expression;
            $Expression = $Expression->GetValueExpression();
        }
        
        $ResolvedTraversalDepth = 0;
        $ResolvedExpression = $this->Accessor->ResolveTraversalExpression(
                array_reverse($TraversalExpressionArray), 
                $PropertyExpression, 
                $ResolvedTraversalDepth);
        
        if($ResolvedExpression !== null) {
            if($ResolvedTraversalDepth === count($TraversalExpressionArray)) {
                return $ResolvedExpression;
            }
            else {
                $ResolvedTraversalExpression = $this->RemoveResolvedTraversal(
                        $ResolvedTraversalDepth, 
                        $TraversalExpression, 
                        $ResolvedExpression);

                return $this->ResolveExcessTraversal($ResolvedTraversalExpression);
            }
        }
    }
    
    private function RemoveResolvedTraversal(
            $ResolvedTraversalDepth, 
            O\TraversalExpression $TraversalExpression,
            O\Expression $NewOriginExpression) {
        if($TraversalExpression->GetTraversalDepth() - 1 === $ResolvedTraversalDepth) {
            return $TraversalExpression->UpdateValue($NewOriginExpression);
        }
        
        return $TraversalExpression->UpdateValue(
                $this->RemoveResolvedTraversal(
                        $ResolvedTraversalDepth, 
                        $TraversalExpression->GetValueExpression(), 
                        $NewOriginExpression));
    }
    
    protected function ResolveExcessTraversal(O\TraversalExpression $ExcessTraversalExpression) {
        return $ExcessTraversalExpression;
    }
    
    /**
     * @param Accessors\Accessor $Accessor
     * @return static
     */
    final public function UpdateProperty(Accessors\Accessor $Accessor) {
        if($this->Accessor === $Accessor) {
            return $this;
        }
    }
    protected abstract function UpdateAccessor(Accessors\Accessor $Accessor);
}

?>