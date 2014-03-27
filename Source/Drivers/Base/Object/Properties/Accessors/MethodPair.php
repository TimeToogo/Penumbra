<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

class MethodPair extends GetterSetter {
    public function __construct($GetterMethodName, $SetterMethodName) {
        parent::__construct(
                new MethodGetter($GetterMethodName), 
                new MethodSetter($SetterMethodName));
    }
}

?>
