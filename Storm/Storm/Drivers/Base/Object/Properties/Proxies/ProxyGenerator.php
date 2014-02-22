<?php

namespace Storm\Drivers\Base\Object\Properties\Proxies;

use \Storm\Drivers\Dynamic\Object\Domain;

abstract class ProxyGenerator implements IProxyGenerator {
    protected $ProxyNamespace;

    public function __construct($ProxyNamespace) {
        $this->ProxyNamespace = $ProxyNamespace;
    }
    
    final protected function GenerateProxyClassName($EntityType) {
        return str_replace('\\', '_', $EntityType) . '_Proxy';
    }
    
    final protected function GetProxyFullName($ProxyClassName) {
        return $this->ProxyNamespace . '\\' . $ProxyClassName;
    }
}

?>