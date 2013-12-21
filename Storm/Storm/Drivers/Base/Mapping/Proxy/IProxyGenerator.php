<?php

namespace Storm\Drivers\Base\Mapping\Proxy;

interface IProxyGenerator {
    public function GenerateProxy($EntityType, callable $EntityLoaderFunction);
}

?>