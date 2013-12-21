<?php

namespace Storm\Drivers\Base\Object\Properties;

class MethodPair extends GetterSetter {
    public function __construct($Name, $IsIdentity, $GetterMethodName, $SetterMethodName) {
        parent::__construct(
                $Name, 
                $IsIdentity, 
                new GetterMethod($GetterMethodName), 
                new SetterMethod($SetterMethodName));
    }
}

?>
