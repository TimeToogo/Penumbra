<?php

namespace Storm\Core\Object;

interface IIdentityProperty extends IProperty {
    const IIdentityType = __CLASS__;
    
    public function Identity(Identity $Identity, $Entity);
}

?>