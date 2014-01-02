<?php

namespace Storm\Core\Object;

interface IRelationshipProperty extends IProperty {
    const IRelationshipPropertyType = __CLASS__;
    
    public function GetEntityType();
    public function IsIdentifying();
    
    public function Revive(Domain $Domain, $PropertyValue, $Entity);
}

?>