<?php

namespace Storm\Drivers\Base\Object\Properties\Proxies;

use \Storm\Core\Object\Domain;
use \Storm\Core\Object\RevivalData;

abstract class ProxyGenerator implements IProxyGenerator {
    protected $ProxyNamespace;
    
    public function __construct($ProxyNamespace) {
        $this->ProxyNamespace = $ProxyNamespace;
    }
    
    final public function GenerateProxies(Domain $Domain, $EntityType, array $AlreadyKnownRevivalDataArray, array $RevivalDataLoaderFunctions) {
        $EntityReflection = new \ReflectionClass($EntityType);
        $ProxyClassName = $this->GenerateProxyClassName($EntityReflection->getName());
        $FullProxyClassName = $this->GetProxyFullName($ProxyClassName);
        
        if(!class_exists($FullProxyClassName, false)) {
            $this->LoadProxyClass($EntityReflection, $ProxyClassName, $FullProxyClassName);
        }
        
        $Proxies = array();
        foreach($RevivalDataLoaderFunctions as $Key => $RevivalDataLoaderFunction) {
            $Proxies[] = $this->InstantiateProxy(
                    $FullProxyClassName, 
                    $Domain, 
                    $AlreadyKnownRevivalDataArray[$Key],
                    $RevivalDataLoaderFunction);
        }
        
        return $Proxies;
    }
    
    final public function GenerateProxy(Domain $Domain, $EntityType, RevivalData $AlreadyKnownRevivalData, callable $RevivalDataLoaderFunction) {
        return $this->GenerateProxies($Domain, $EntityType, [$AlreadyKnownRevivalData], [$RevivalDataLoaderFunction])[0];
    }
    
    final private function InstantiateProxy(
            $FullProxyName, 
            Domain $Domain,
            RevivalData $AlreadyKnownRevivalData, 
            callable $RevivalDataLoaderFunction) {
        return new $FullProxyName($Domain, $AlreadyKnownRevivalData, $RevivalDataLoaderFunction);
    }
    
    protected abstract function LoadProxyClass(
            \ReflectionClass $EntityReflection, 
            $ProxyClassName,
            $FullProxyName);


    final protected function GenerateProxyClassName($EntityType) {
        return str_replace('\\', '_', $EntityType) . '_Proxy';
    }
    
    final protected function GetProxyFullName($ProxyClassName) {
        return $this->ProxyNamespace . '\\' . $ProxyClassName;
    }
}

?>