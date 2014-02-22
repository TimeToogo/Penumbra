<?php

namespace Storm\Drivers\Base\Object\Properties\Proxies;

use \Storm\Core\Object;

class EvalProxyGenerator extends ProxyGenerator {
    private $ConcreteProxyDataGenerator;
    
    public function __construct($ProxyNamespace) {
        parent::__construct($ProxyNamespace);
        $this->ConcreteProxyDataGenerator = new ConcreteProxyDataGenerator();
    }
    
    public function GenerateProxies(Object\Domain $Domain, $EntityType, 
            array $AlreadyKnownRevivalDataArray,
            array $RevivalDataLoaderFunctions) {
        $EntityReflection = new \ReflectionClass($EntityType);
        $ProxyClassName = $this->GenerateProxyClassName($EntityReflection->getName());
        $FullProxyName = $this->GetProxyFullName($ProxyClassName);
        
        $Proxies = [];
        foreach($RevivalDataLoaderFunctions as $Key => $RevivalDataLoaderFunction) {
            $Proxies[] = $this->GenerateProxyInstance($Domain, $EntityReflection, $ProxyClassName, $FullProxyName, 
                    $AlreadyKnownRevivalDataArray[$Key],
                    $RevivalDataLoaderFunction);
        }
        
        return $Proxies;
    }

    public function GenerateProxy(Object\Domain $Domain, $EntityType, 
            Object\RevivalData $AlreadyKnownRevivalData,
            callable $RevivalDataLoaderFunction) {
        $EntityReflection = new \ReflectionClass($EntityType);
        $ProxyClassName = $this->GenerateProxyClassName($EntityReflection->getName());
        $FullProxyName = $this->GetProxyFullName($ProxyClassName);
        
        return $this->GenerateProxyInstance($Domain, $EntityReflection, $ProxyClassName, $FullProxyName, 
                $AlreadyKnownRevivalData, $RevivalDataLoaderFunction);
    }
    
    private function GenerateProxyInstance(Object\Domain $Domain, $EntityReflection, $ProxyClassName, $FullProxyName, 
            Object\RevivalData $AlreadyKnownRevivalData,
            callable $RevivalDataLoaderFunction) {
        if(class_exists($FullProxyName, false)) {
            return new $FullProxyName($Domain, $AlreadyKnownRevivalData, $RevivalDataLoaderFunction);
        }
        else {
            eval($this->ConcreteProxyDataGenerator->GenerateConcreteProxyData($this->ProxyNamespace, $ProxyClassName, $EntityReflection));
            
            return new $FullProxyName($Domain, $AlreadyKnownRevivalData, $RevivalDataLoaderFunction);
        }
    }
}

?>