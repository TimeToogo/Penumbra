<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

use \Penumbra\Core\Object\Expressions;
use \Penumbra\Core\Object\Expressions\TraversalExpression;

class FieldSetter extends FieldBase implements IPropertySetter {
    
    public function SetValueTo($Entity, $Value) {
        $this->Reflection->setValue($Entity, $Value);
    }
}

?>