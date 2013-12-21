<?php

namespace Storm\Drivers\Base\Object\Properties;

class GetterField extends Field implements IPropertyGetter {
    
    public function CanGetValueFrom($EntityType) {
        return $this->ValidPropertyOf($EntityType);
    }

    public function &GetValueFrom($Entity) {
        if($this->IsPublic)
            return $Entity->{$this->Name};
        else
            return $this->GetReflectionProperty($Entity)->getValue($Entity);
    }

}

?>
