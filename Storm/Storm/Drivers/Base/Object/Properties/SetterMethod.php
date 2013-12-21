<?php

namespace Storm\Drivers\Base\Object\Properties;

class SetterMethod extends Method implements IPropertySetter {

    public function CanSetValueTo($EntityType) {
        return $this->ValidMethodOf($EntityType);
    }

    public function SetValueTo($Entity, &$Value) {
        if($this->IsPublic)
            return $Entity->{$this->MethodName}($Value);
        else
            return $this->GetReflectionMethod($Entity)->invoke($Entity, $Value);
    }
}

?>