<?php

namespace Storm\Drivers\Base\Object\Properties;

class CustomProperty extends GetterSetter {
    public function __construct($Name, $IsIdentity, callable $GetterFunction, callable $SetterFunction) {
        parent::__construct(
                $Name, 
                $IsIdentity, 
                new CustomGetter($GetterFunction), 
                new CustomSetter($SetterFunction));
    }
}

?>
