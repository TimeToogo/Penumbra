<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class MethodGetter extends MethodBase implements IPropertyGetter {
    public function GetValueFrom($Entity) {
        return $this->Reflection->invoke($Entity, $this->ConstantArguments);
    }
}

?>
