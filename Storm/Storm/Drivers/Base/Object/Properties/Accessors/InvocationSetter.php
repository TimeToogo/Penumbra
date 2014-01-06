<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class InvocationSetter extends InvocationBase implements IPropertySetter {
    
    public function SetValueTo($Entity, $Value) {
        $this->Reflection->invokeArgs($Entity, array_merge($this->ConstantArguments, [$Value]));
    }
}

?>