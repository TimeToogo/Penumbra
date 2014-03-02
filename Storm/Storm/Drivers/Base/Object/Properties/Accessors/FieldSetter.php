<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions;
use \Storm\Core\Object\Expressions\TraversalExpression;

class FieldSetter extends FieldBase implements IPropertySetter {
    
    public function SetValueTo($Entity, $Value) {
        $this->Reflection->setValue($Entity, $Value);
    }
}

?>