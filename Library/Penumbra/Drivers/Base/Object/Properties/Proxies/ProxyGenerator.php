<?php

namespace Penumbra\Drivers\Base\Object\Properties\Proxies;

use \Penumbra\Core\Object\Domain;
use \Penumbra\Core\Object\IEntityMap;
use \Penumbra\Core\Object\RevivalData;

abstract class ProxyGenerator implements IProxyGenerator {
    protected $ProxyNamespace;
    
    public function __construct($ProxyNamespace) {
        $this->ProxyNamespace = $ProxyNamespace;
    }
    
    final public function GenerateProxies(IEntityMap $EntityMap, array $AlreadyKnownRevivalDataArray, array $RevivalDataLoaderFunctions) {
        $EntityReflection = new \ReflectionClass($EntityMap->GetEntityType());
        $ProxyClassName = $this->GenerateProxyClassName($EntityReflection->getName());
        $FullProxyClassName = $this->GetProxyFullName($ProxyClassName);
        
        if(!class_exists($FullProxyClassName, false)) {
            $this->LoadProxyClass($EntityReflection, $ProxyClassName, $FullProxyClassName);
        }
        
        $Proxies = array();
        foreach($RevivalDataLoaderFunctions as $Key => $RevivalDataLoaderFunction) {
            $Proxies[] = $this->InstantiateProxy(
                    $FullProxyClassName, 
                    $EntityMap, 
                    $AlreadyKnownRevivalDataArray[$Key],
                    $RevivalDataLoaderFunction);
        }
        
        return $Proxies;
    }
    
    final public function GenerateProxy(IEntityMap $EntityMap, RevivalData $AlreadyKnownRevivalData, callable $RevivalDataLoaderFunction) {
        return $this->GenerateProxies($EntityMap, [$AlreadyKnownRevivalData], [$RevivalDataLoaderFunction])[0];
    }
    
    final private function InstantiateProxy(
            $FullProxyName, 
            IEntityMap $EntityMap, 
            RevivalData $AlreadyKnownRevivalData, 
            callable $RevivalDataLoaderFunction) {
        return new $FullProxyName($EntityMap, $AlreadyKnownRevivalData, $RevivalDataLoaderFunction);
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