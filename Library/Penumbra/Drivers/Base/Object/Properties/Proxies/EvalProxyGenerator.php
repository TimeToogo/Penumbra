<?php

namespace Penumbra\Drivers\Base\Object\Properties\Proxies;

class EvalProxyGenerator extends ProxyGenerator {
    private $ConcreteProxyDataGenerator;
    
    public function __construct($ProxyNamespace) {
        parent::__construct($ProxyNamespace);
        $this->ConcreteProxyDataGenerator = new ConcreteProxyDataGenerator();
    }
    
    protected function LoadProxyClass(\ReflectionClass $EntityReflection, $ProxyClassName, $FullProxyName) {
        eval($this->ConcreteProxyDataGenerator->GenerateConcreteProxyData($this->ProxyNamespace, $ProxyClassName, $EntityReflection));
    }

}

?>