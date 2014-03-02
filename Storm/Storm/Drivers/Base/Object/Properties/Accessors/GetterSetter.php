<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\TraversalExpression;
use \Storm\Core\Object\Expressions\PropertyExpression;

class GetterSetter extends Accessor {
    private $PropertyGetter;
    private $PropertySetter;
    
    public function __construct(
            IPropertyGetter $PropertyGetter, 
            IPropertySetter $PropertySetter) {
        $this->PropertyGetter = $PropertyGetter;
        $this->PropertySetter = $PropertySetter;
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
    
    public function ParseTraversalExpression(TraversalExpression $Expression, PropertyExpression $PropertyExpression) {
        $GetterExpression = $this->PropertyGetter->ParseTraversalExpression($Expression, $PropertyExpression);
        if($GetterExpression !== null) {
            return $GetterExpression;
        } 
        else {
            return $this->PropertySetter->ParseTraversalExpression($Expression, $PropertyExpression);
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
