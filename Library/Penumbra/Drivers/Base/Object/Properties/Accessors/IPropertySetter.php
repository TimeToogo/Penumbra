<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

use \Penumbra\Core\Object\Expressions as O;

interface IPropertySetter {
    public function Identifier(&$Identifier);
    public function GetTraversalDepth();
    public function ResolveTraversalExpression(array $TraversalExpressions, O\PropertyExpression $PropertyExpression);
    
    public function SetEntityType($EntityType);
    public function SetValueTo($Entity, $Value);
}

?>
