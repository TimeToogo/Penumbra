<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class GetterMethod extends MethodBase implements IPropertyGetter {

    public function GetValueFrom($Entity) {
        return $this->Reflection->invoke($Entity);
    }
}

?>
