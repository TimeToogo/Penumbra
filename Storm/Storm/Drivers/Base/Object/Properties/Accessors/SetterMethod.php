<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class SetterMethod extends MethodBase implements IPropertySetter {

    public function SetValueTo($Entity, &$Value) {
        $this->Reflection->invokeArgs($Entity, [&$Value]);
    }
}

?>