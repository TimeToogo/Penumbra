<?php

namespace Storm\Drivers\Base\Object\Properties\Proxies;

use \Storm\Core\Object\Domain;

class ProductionProxyGenerator extends ProxyGenerator {
    public function GenerateProxies(Domain $Domain, $EntityType, array $RevivalDataLoaderFunctions) {
        $ProxyClassName = $this->GenerateProxyClassName($EntityType);
        $FullProxyName = $this->GetProxyFullName($ProxyClassName);
        $EntityMap = $Domain->GetEntityMap($EntityType);
        
        if(!class_exists($FullProxyName, false)) {
            $ProxyFileName = $this->GenerateProxyFileName($ProxyClassName);
            require $ProxyFileName;
        }
        
        $Proxies = array();
        foreach($RevivalDataLoaderFunctions as $RevivalDataLoaderFunction) {
            $Proxies[] = new $FullProxyName($EntityMap, $RevivalDataLoaderFunction);
        }
        
        return $Proxies;
    }
    
    public function GenerateProxy(Domain $Domain, $EntityType, callable $RevivalDataLoaderFunction) {
        $ProxyClassName = $this->GenerateProxyClassName($EntityType);
        $FullProxyName = $this->GetProxyFullName($ProxyClassName);
        
        if(class_exists($FullProxyName, false)) {
            return new $FullProxyName($Domain->GetEntityMap($EntityType), $RevivalDataLoaderFunction);
        }
        else {
            $ProxyFileName = $this->GenerateProxyFileName($ProxyClassName);
            require $ProxyFileName;
            return new $FullProxyName($Domain->GetEntityMap($EntityType), $RevivalDataLoaderFunction);
        }
    }
}

?>