<?php

namespace Storm\Drivers\Base\Object\Properties;

class HiddenMethodPair extends GetterSetter {
    public function __construct($Name, $IsIdentity, $GetterMethodName, $SetterMethodName) {
        parent::__construct(
                $Name, 
                $IsIdentity, 
                new HiddenGetterMethod($GetterMethodName), 
                new HiddenSetterMethod($SetterMethodName));
    }
}

?>
