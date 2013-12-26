<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class Custom extends GetterSetter {
    public function __construct(callable $GetterFunction, callable $SetterFunction) {
        parent::__construct(
                new CustomGetter($GetterFunction), 
                new CustomSetter($SetterFunction));
    }
}

?>
