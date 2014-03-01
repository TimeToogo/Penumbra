<?php

namespace Storm\Drivers\Base\Object\Properties\Proxies;

use \Storm\Core\Object\IEntityMap;
use \Storm\Core\Object\RevivalData;

interface IProxyGenerator {
    public function GenerateProxy(
            IEntityMap $EntityMap, 
            RevivalData $AlreadyKnownRevivalData,
            callable $RevivalDataLoaderFunction);
    
    public function GenerateProxies(
            IEntityMap $EntityMap, 
            array $AlreadyKnownRevivalDataArray, 
            array $RevivalDataLoaderFunctions);
}

?>