<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class Field extends GetterSetter {
    private $FieldName;
    
    public function __construct($FieldName) {        
        parent::__construct(
                new FieldGetter($FieldName), 
                new FieldSetter($FieldName));
    }
    
    final public function GetFieldName() {
        return $this->FieldName;
    }
}

?>