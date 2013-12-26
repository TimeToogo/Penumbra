<?php

namespace Storm\Core\Object;

interface IDataProperty extends IProperty {
    const IDataPropertyType = __CLASS__;
    public function IsIdentity();
    
    public function ReviveValue($PropertyValue, $Entity);
    public function GetValue($Entity);
}

?>