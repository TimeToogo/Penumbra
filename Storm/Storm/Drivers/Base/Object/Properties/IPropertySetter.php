<?php

namespace Storm\Drivers\Base\Object\Properties;

interface IPropertySetter {
    public function CanSetValueTo($EntityType);
    public function SetValueTo($Entity, &$Value);
}

?>
