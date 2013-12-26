<?php

namespace Storm\Drivers\Base\Object\Properties\Proxies;

use \Storm\Core\Object\Domain;

interface IProxyGenerator {
    public function GenerateProxy(Domain $Domain, $EntityType, callable $RevivalDataLoaderFunction);
    public function GenerateProxies(Domain $Domain, $EntityType, array $RevivalDataLoaderFunctions);
}

?>