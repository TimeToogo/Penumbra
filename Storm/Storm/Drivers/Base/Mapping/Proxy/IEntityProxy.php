<?php

namespace Storm\Drivers\Base\Mapping\Proxy;

interface IEntityProxy {
    const IEntityProxyType = __CLASS__;
    
    public function __IsLoaded();
}

?>