<?php

namespace Storm\Drivers\Base\Mapping\Proxy;

use \Storm\Drivers\Dynamic\Object\Domain;

abstract class ProxyGenerator implements IProxyGenerator {
    private $Domain;
    protected $ProxyNamespace;
    protected $ProxyCachePath;

    public function __construct(Domain $Domain, $ProxyNamespace, $ProxyCachePath) {
        $this->Domain = $Domain;
        $this->ProxyNamespace = $ProxyNamespace;
        if($ProxyCachePath[strlen($ProxyCachePath) - 1] !== DIRECTORY_SEPARATOR) {
            $ProxyCachePath .= DIRECTORY_SEPARATOR;
        }
        $this->ProxyCachePath = $ProxyCachePath;
    }
    
    /**
     * @return Domain
     */
    final public function GetDomain() {
        return $this->Domain;
    }

    final protected function GenerateProxyClassName($EntityType) {
        return str_replace('\\', '_', $EntityType) . '__Proxy';
    }
    
    final protected function GetProxyFullName($ProxyClassName) {
        return $this->ProxyNamespace . '\\' . $ProxyClassName;
    }

    final protected function GenerateProxyFileName($ProxyClassName) {
        return $this->ProxyCachePath . $ProxyClassName . '.php';
    }
}

?>