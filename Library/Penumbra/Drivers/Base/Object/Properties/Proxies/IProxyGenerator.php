<?php

namespace Penumbra\Drivers\Base\Object\Properties\Proxies;

use \Penumbra\Core\Object\IEntityMap;
use \Penumbra\Core\Object\RevivalData;

interface IProxyGenerator {
    const IProxyGeneratorType = __CLASS__;
    
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