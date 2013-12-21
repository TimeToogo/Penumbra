<?php

namespace Storm\Drivers\Base\Object\Properties;

class HiddenGetterMethod extends GetterMethod {
    public function __construct($MethodName) {
        parent::__construct($MethodName, false);
    }
}

?>
