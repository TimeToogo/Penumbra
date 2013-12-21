<?php

namespace Storm\Drivers\Base\Object\Properties;

class HiddenSetterMethod extends SetterMethod {
    public function __construct($MethodName) {
        parent::__construct($MethodName, false);
    }
}

?>