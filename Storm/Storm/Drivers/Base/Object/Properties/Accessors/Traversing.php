<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\TraversalExpression;
use \Storm\Core\Object\Expressions\PropertyExpression;

class Traversing extends Accessor {
    /**
     * @var Accessor[] 
     */
    private $NestedAccessors = [];
    /**
     * @var Accessor[] 
     */
    private $TraversingAccessors = [];
    /**
     * @var Accessor
     */
    private $FinalAccessor = [];
    
    public function __construct(array $NestedAccessors) {
        if(count($NestedAccessors) === 0) {
            throw new Object\ObjectException('The supplied nested accessors must contain atleast one accessor');
        }
        
        foreach($NestedAccessors as $NestedProperty) {
            $this->Add($NestedProperty);
        }
        $this->TraversingAccessors = $this->NestedAccessors;
        $this->FinalAccessor = array_pop($this->TraversingAccessors);
        parent::__construct();
    }
    
    protected function Identifier(&$Identifier) {
        foreach($this->NestedAccessors as $NestedAccessor) {
            $NestedAccessor->Identifier($Identifier);
        }
    }
    
    public function ResolveTraversalExpression(array $TraversalExpressions, O\PropertyExpression $PropertyExpression, &$ReturnTotalResolutionDepth) {
        $TotalResolutionDepth = 0;
                
        $Expression = $Expression->GetValueExpression();
        foreach($this->TraversingAccessors as $TraversingAccessor) {
            $ResolutionDepth = 0;
            
            if($TraversingAccessor->ResolveTraversalExpression($TraversalExpressions, $PropertyExpression, $ResolutionDepth) !== null) {
                $TraversalExpressions = array_slice($TraversalExpressions, $ResolutionDepth);
                $TotalResolutionDepth += $ResolutionDepth;
            }
            else {
                return;
            }
        }
        
        $ResolutionDepth = 0;
        $ResolvedExpression = $this->FinalAccessor->ResolveTraversalExpression($TraversalExpressions, $PropertyExpression, $ResolutionDepth);
        if($ResolvedExpression === null) {
            return;
        }
                
        $ReturnTotalResolutionDepth = $TotalResolutionDepth;
        return $ResolvedExpression;
    }
    
    public function __clone() {
        foreach($this->NestedAccessors as $Key => $NestedAccessor) {
            $this->NestedAccessors[$Key] = clone $NestedAccessor;
        }
    }
    
    private function Add(Accessor $Accessor) {
        if($Accessor instanceof self) {
            $this->NestedAccessors = array_merge($this->NestedAccessors, $Accessor->NestedAccessors);
        }
        else {
            $this->NestedAccessors[] = $Accessor;
        }
    }
    
    /**
     * @return Accessor[]
     */
    final public function GetNestedAccessors() {
        return $this->NestedAccessors;
    }
    
    private function GetTraversedValue($Entity) {
        $Value = $Entity;
        foreach($this->TraversingAccessors as $TraversingAccessor) {
            $TraversingAccessor->SetEntityType(get_class($Value));
            $Value = $TraversingAccessor->GetValue($Value);
        }
        return $Value;
    }
    final public function GetValue($Entity) {
        $Value = $this->GetTraversedValue($Entity);
        $this->FinalAccessor->SetEntityType(get_class($Value));
        
        return $this->FinalAccessor->GetValue($Value);
    }

    final public function SetValue($Entity, $Value) {
        $TraversedValue = $this->GetTraversedValue($Entity);
        $this->FinalAccessor->SetEntityType(get_class($TraversedValue));
                
        $this->FinalAccessor->SetValue($TraversedValue, $Value);
    }
}

?>