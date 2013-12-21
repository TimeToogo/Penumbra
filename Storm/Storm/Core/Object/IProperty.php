<?php

namespace Storm\Core\Object;

interface IProperty {
    const IPropertyType = __CLASS__;
    
    public function GetName();
    public function IsIdentity();
    
    public function ValidPropertyOf($EntityType);
    public function CanGetValue();
    public function &GetValue($Entity);
    public function CanSetValue();
    public function SetValue($Entity, &$Value);
}

?>