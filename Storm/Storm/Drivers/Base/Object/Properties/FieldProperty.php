<?php

namespace Storm\Drivers\Base\Object\Properties;

class FieldProperty extends GetterSetter {
    public function __construct($FieldName, $IsIdentity = false, $Name = null) {
        if($Name === null)
            $Name = $FieldName;
        
        parent::__construct(
                $Name, 
                $IsIdentity, 
                new GetterField($FieldName), 
                new SetterField($FieldName));
    }
}

?>