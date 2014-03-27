<?php

namespace Penumbra\Drivers\Base\Object\Properties\Proxies;

use \Penumbra\Drivers\Dynamic\Object\Domain;

abstract class ProxyFileGenerator extends ProxyGenerator {
    protected $ProxyCachePath;

    public function __construct($ProxyNamespace, $ProxyCachePath) {
        parent::__construct($ProxyNamespace);
        if($ProxyCachePath[strlen($ProxyCachePath) - 1] !== DIRECTORY_SEPARATOR) {
            $ProxyCachePath .= DIRECTORY_SEPARATOR;
        }
        $this->ProxyCachePath = $ProxyCachePath;
    }

    final protected function GenerateProxyFileName($ProxyClassName) {
        return $this->ProxyCachePath . $ProxyClassName . '.php';
    }
    
    final protected function LoadProxyClass(\ReflectionClass $EntityReflection, $ProxyClassName, $FullProxyName) {
        $ProxyFileName = $this->GenerateProxyFileName($ProxyClassName);
        
        return $this->LoadProxyClassFile($EntityReflection, $ProxyClassName, $FullProxyName, $ProxyFileName);
    }
    protected abstract function LoadProxyClassFile(
            \ReflectionClass $EntityReflection, 
            $ProxyClassName, 
            $FullProxyName,
            $ProxyFileName);
}

?>