<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions as O;

interface IPropertySetter {
    public function Identifier(&$Identifier);
    public function ResolveTraversalExpression(O\TraversalExpression $Expression, O\PropertyExpression $PropertyExpression);
    
    public function SetEntityType($EntityType);
    public function SetValueTo($Entity, $Value);
}

?>
