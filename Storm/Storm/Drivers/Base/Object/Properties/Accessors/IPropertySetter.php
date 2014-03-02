<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

interface IPropertySetter {
    public function Identifier(&$Identifier);
    public abstract function ParseTraversalExpression(TraversalExpression $Expression, PropertyExpression $PropertyExpression);
    
    public function SetEntityType($EntityType);
    public function SetValueTo($Entity, $Value);
}

?>
