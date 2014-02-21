<?php

namespace Storm\Drivers\Base\Object\Properties\Proxies;

use \Storm\Core\Object\Domain;
use \Storm\Core\Object\RevivalData;

class ProductionProxyGenerator extends ProxyGenerator {
    public function GenerateProxies(Domain $Domain, $EntityType, array $AlreadyKnownRevivalDataArray, array $RevivalDataLoaderFunctions) {
        $ProxyClassName = $this->GenerateProxyClassName($EntityType);
        $FullProxyName = $this->GetProxyFullName($ProxyClassName);
        $EntityMap = $Domain->GetEntityMap($EntityType);
        
        if(!class_exists($FullProxyName, false)) {
            $ProxyFileName = $this->GenerateProxyFileName($ProxyClassName);
            require $ProxyFileName;
        }
        
        $Proxies = [];
        foreach($RevivalDataLoaderFunctions as $Key => $RevivalDataLoaderFunction) {
            $Proxies[] = new $FullProxyName($EntityMap, $AlreadyKnownRevivalDataArray[$Key], $RevivalDataLoaderFunction);
        }
        
        return $Proxies;
    }
    
    public function GenerateProxy(Domain $Domain, $EntityType, RevivalData $AlreadyKnownRevivalData, callable $RevivalDataLoaderFunction) {
        $ProxyClassName = $this->GenerateProxyClassName($EntityType);
        $FullProxyName = $this->GetProxyFullName($ProxyClassName);
        
        if(class_exists($FullProxyName, false)) {
            return new $FullProxyName($Domain, $AlreadyKnownRevivalData, $RevivalDataLoaderFunction);
        }
        else {
            $ProxyFileName = $this->GenerateProxyFileName($ProxyClassName);
            require $ProxyFileName;
            return new $FullProxyName($Domain, $AlreadyKnownRevivalData, $RevivalDataLoaderFunction);
        }
    }
}

?>