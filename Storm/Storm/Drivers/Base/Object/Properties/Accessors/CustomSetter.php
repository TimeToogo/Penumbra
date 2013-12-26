<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class CustomSetter extends CustomBase implements IPropertySetter {
    public function __construct(callable $SetterFunction) {
        parent::__construct($SetterFunction);
    }

    final public function CanSetValueTo($EntityType) {
        return $this->ValidCustomOf($EntityType);
    }

    final public function SetValueTo($Entity, &$Value) {
        return $this->CallFunction([$Entity, $Value]); 
    }
}

?>
