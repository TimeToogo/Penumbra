<?php

namespace Storm\Drivers\Base\Object\Properties;

class HiddenField extends GetterSetter {
    public function __construct($PropertyName, $IsIdentity = false, $Name = null) {
        if($Name === null)
            $Name = $PropertyName;
        
        parent::__construct(
                $Name, 
                $IsIdentity, 
                new HiddenGetterField($PropertyName), 
                new HiddenSetterField($PropertyName));
    }
}

?>