<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

use \Penumbra\Core\Object\Expressions;
use \Penumbra\Core\Object\Expressions\TraversalExpression;

class IndexSetter extends IndexBase implements IPropertySetter {
        
    final public function SetValueTo($Entity, $Value) {
        $Entity[$this->Index] = $Value;
    }
}

?>
