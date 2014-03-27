<?php

namespace Penumbra\Drivers\Base\Object\Properties\Proxies;

interface IProxy {
    const IProxyType = __CLASS__;
    
    public function __IsLoaded();
    public function __IsAltered();
    public function __CloneProxyInstance();
}

?>