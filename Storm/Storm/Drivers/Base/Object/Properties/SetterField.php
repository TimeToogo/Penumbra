<?php

namespace Storm\Drivers\Base\Object\Properties;

class SetterField extends Field implements IPropertySetter {

    public function CanSetValueTo($EntityType) {
        return $this->ValidPropertyOf($EntityType);
    }

    public function SetValueTo($Entity, &$Value) {
        if($this->IsPublic)
            $Entity->{$this->Name} = $Value;
        else
            $this->GetReflectionProperty($Entity)->setValue($Entity, $Value);
    }

}

?>