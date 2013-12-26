<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class MethodPair extends GetterSetter {
    public function __construct($GetterMethodName, $SetterMethodName) {
        parent::__construct(
                new GetterMethod($GetterMethodName), 
                new SetterMethod($SetterMethodName));
    }
}

?>
