<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

interface IPropertyGetter {
    public function Identifier(&$Identifier);
    public function SetEntityType($EntityType);
    public function GetValueFrom($Entity);
}

?>
