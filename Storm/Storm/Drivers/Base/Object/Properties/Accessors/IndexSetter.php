<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class IndexSetter extends IndexBase implements IPropertySetter {

    final public function SetValueTo($Entity, $Value) {
        $Entity[$this->Index] = $Value;
    }
}

?>
