<?php

namespace Storm\Drivers\Base\Object\Properties;

interface IPropertyGetter {
    public function CanGetValueFrom($EntityType);
    public function &GetValueFrom($Entity);
}

?>
