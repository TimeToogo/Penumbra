<?php

namespace Storm\Drivers\Base\Object\Properties\Proxies;

use \Storm\Core\Object\Domain;
use \Storm\Core\Object\RevivalData;

/**
 * Assumes that all proxy files have been generated.
 */
class ProductionProxyGenerator extends ProxyFileGenerator {
    protected function LoadProxyClassFile(\ReflectionClass $EntityReflection, $ProxyClassName, $FullProxyName, $ProxyFileName) {
        require_once $ProxyClassName;
    }
}

?>