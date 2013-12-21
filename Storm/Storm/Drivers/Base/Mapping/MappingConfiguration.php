<?php

namespace Storm\Drivers\Base\Mapping;

class MappingConfiguration implements IMappingConfiguration {
    private $DefaultLoadingMode;
    private $ProxyGenerator;
    
    public function __construct(
            $DefaultLoadingMode, 
            Proxy\IProxyGenerator $ProxyGenerator) {
        $this->DefaultLoadingMode = $DefaultLoadingMode;
        $this->ProxyGenerator = $ProxyGenerator;
    }
    
    final public function GetDefaultLoadingMode() {
        return $this->DefaultLoadingMode;
    }

    final public function GetProxyGenerator() {
        return $this->ProxyGenerator;
    }
}

?>