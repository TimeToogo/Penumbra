<?php

namespace Penumbra\Drivers\Base\Object\Properties\Proxies;

use \Penumbra\Core\Object\Domain;
use \Penumbra\Core\Object\RevivalData;

/**
 * Assumes that all proxy files have been generated.
 */
class ProductionProxyGenerator extends ProxyFileGenerator {
    protected function LoadProxyClassFile(\ReflectionClass $EntityReflection, $ProxyClassName, $FullProxyName, $ProxyFileName) {
        require_once $ProxyFileName;
    }
}

?>