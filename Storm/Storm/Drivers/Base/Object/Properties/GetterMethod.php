<?php

namespace Storm\Drivers\Base\Object\Properties;

class GetterMethod extends Method implements IPropertyGetter {

    public function CanGetValueFrom($EntityType) {
        return $this->ValidMethodOf($EntityType);
    }

    public function GetValueFrom(&$Entity) {
        if($this->IsPublic)
            return $Entity->{$this->MethodName}();
        else
            return $this->GetReflectionMethod($Entity)->invoke($Entity);
    }
}

?>
