<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class CustomGetter extends CustomBase implements IPropertyGetter {
    public function __construct(callable $GetterFunction) {
        parent::__construct($GetterFunction);
    }

    final public function GetValueFrom($Entity) {
        return $this->CallFunction([$Entity]);
    }
}

?>
