<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions\TraversalExpression;
use \Storm\Core\Object\Expressions\PropertyExpression;

interface IPropertyGetter {
    public function Identifier(&$Identifier);
    public abstract function ParseTraversalExpression(TraversalExpression $Expression, PropertyExpression $PropertyExpression);
    
    public function SetEntityType($EntityType);
    public function GetValueFrom($Entity);
}

?>
