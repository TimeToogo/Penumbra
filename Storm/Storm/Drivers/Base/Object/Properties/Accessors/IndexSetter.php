<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions;
use \Storm\Core\Object\Expressions\TraversalExpression;

class IndexSetter extends IndexBase implements IPropertySetter {
        
    final public function SetValueTo($Entity, $Value) {
        $Entity[$this->Index] = $Value;
    }
}

?>
