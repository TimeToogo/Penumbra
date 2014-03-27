<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

use \Penumbra\Core\Object\Expressions as O;

class GetterSetter extends Accessor {
    private $PropertyGetter;
    private $PropertyGetterTraversalDepth;
    private $PropertySetter;
    private $PropertySetterTraversalDepth;
    
    public function __construct(
            IPropertyGetter $PropertyGetter, 
            IPropertySetter $PropertySetter) {
        $this->PropertyGetter = $PropertyGetter;
        $this->PropertyGetterTraversalDepth = $PropertyGetter->GetTraversalDepth();
        $this->PropertySetter = $PropertySetter;
        $this->PropertySetterTraversalDepth = $PropertySetter->GetTraversalDepth();
        parent::__construct();
    }

    protected function Identifier(&$Identifier) {
        $GetterIdentifier = '';
        $SetterIdentifier = '';
        $this->PropertyGetter->Identifier($GetterIdentifier);
        $this->PropertySetter->Identifier($SetterIdentifier);
        
        if($GetterIdentifier === $SetterIdentifier) {
            $Identifier .= $GetterIdentifier;
        }
        else {
            $Identifier .= '{' . $GetterIdentifier . '|' . $SetterIdentifier . '}';
        }
    }
    
    public function ResolveTraversalExpression(array $TraversalExpressions, O\PropertyExpression $PropertyExpression, &$ResolutionDepth) {
        $TraversalDepth = count($TraversalExpressions);
        
        if($TraversalDepth >= $this->PropertyGetterTraversalDepth) {
            $ResolvedExpression = $this->PropertyGetter->ResolveTraversalExpression($TraversalExpressions, $PropertyExpression);
            if($ResolvedExpression !== null) {
                $ResolutionDepth = $this->PropertyGetterTraversalDepth;
                return $ResolvedExpression;
            }
        }
        
        if($TraversalDepth >= $this->PropertySetterTraversalDepth) {
            $ResolvedExpression = $this->PropertySetter->ResolveTraversalExpression($TraversalExpressions, $PropertyExpression);
            if($ResolvedExpression !== null) {
                $ResolutionDepth = $this->PropertySetterTraversalDepth;
                return $ResolvedExpression;
            }
        }
    }
    
    /**
     * @return IPropertyGetter
     */
    public function GetPropertyGetter() {
        return $this->PropertyGetter;
    }
    
    /**
     * @return IPropertySetter
     */
    public function GetPropertySetter() {
        return $this->PropertySetter;
    }
    
    public function SetEntityType($EntityType) {
        parent::SetEntityType($EntityType);
        $this->PropertyGetter->SetEntityType($EntityType);
        $this->PropertySetter->SetEntityType($EntityType);
    }

    final public function GetValue($Entity) {
        return $this->PropertyGetter->GetValueFrom($Entity);
    }

    final public function SetValue($Entity, $Value) {
        $this->PropertySetter->SetValueTo($Entity, $Value);
    }

}

?>
