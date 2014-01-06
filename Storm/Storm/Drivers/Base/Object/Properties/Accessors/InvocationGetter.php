<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class InvocationGetter extends InvocationBase implements IPropertyGetter {
    
    public function GetValueFrom($Entity) {
        return $this->Reflection->invokeArgs($Entity, $this->ConstantArguments);
    }
}

?>
