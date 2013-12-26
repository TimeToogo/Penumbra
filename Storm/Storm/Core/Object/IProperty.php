<?php

namespace Storm\Core\Object;

interface IProperty {
    const IPropertyType = __CLASS__;
    
    public function GetIdentifier();
    /**
     * @return EntityMap
     */
    public function GetEntityMap();
    public function HasEntityMap();
    public function SetEntityMap(EntityMap $EntityMap = null);
}

?>