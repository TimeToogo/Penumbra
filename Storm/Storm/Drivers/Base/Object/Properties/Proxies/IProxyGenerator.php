<?php

namespace Storm\Drivers\Base\Object\Properties\Proxies;

use \Storm\Core\Object\Domain;
use \Storm\Core\Object\RevivalData;
interface IProxyGenerator {
    public function GenerateProxy(Domain $Domain, $EntityType, 
            RevivalData $AlreadyKnownRevivalData,
            callable $RevivalDataLoaderFunction);
    
    public function GenerateProxies(Domain $Domain, $EntityType, 
            array $AlreadyKnownRevivalDataArray, 
            array $RevivalDataLoaderFunctions);
}

?>