<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

interface IPropertySetter {
    public function Identifier(&$Identifier);
    public function SetEntityType($EntityType);
    public function SetValueTo($Entity, &$Value);
}

?>
