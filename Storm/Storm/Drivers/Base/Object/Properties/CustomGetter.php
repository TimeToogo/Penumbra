<?php

namespace Storm\Drivers\Base\Object\Properties;

class CustomGetter extends Custom implements IPropertyGetter {
    public function __construct(callable $GetterFunction) {
        parent::__construct($GetterFunction);
    }

    final public function CanGetValueFrom($EntityType) {
        return $this->ValidCustomOf($EntityType);
    }

    final public function GetValueFrom($Entity) {
        return $this->CallFunction([$Entity]);
    }
}

?>
