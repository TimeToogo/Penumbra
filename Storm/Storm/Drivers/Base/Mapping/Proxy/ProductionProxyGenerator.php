<?php

namespace Storm\Drivers\Base\Mapping\Proxy;

class ProductionProxyGenerator extends ProxyGenerator {
    
    public function GenerateProxy($EntityType, callable $EntityLoaderFunction) {
        
        $ProxyClassName = $this->GenerateProxyClassName($EntityType);
        $FullProxyName = $this->GetProxyFullName($ProxyClassName);
        
        if(class_exists($FullProxyName)) {
            return new $FullProxyName($EntityLoaderFunction);
        }
        else {
            $ProxyFileName = $this->GenerateProxyFileName($ProxyClassName);
            require $ProxyFileName;
            return new $FullProxyName($EntityLoaderFunction);
        }
    }
}

?>