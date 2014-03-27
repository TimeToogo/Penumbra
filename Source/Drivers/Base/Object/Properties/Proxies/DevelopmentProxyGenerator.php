<?php

namespace Penumbra\Drivers\Base\Object\Properties\Proxies;

use \Penumbra\Core\Object;

class DevelopmentProxyGenerator extends ProxyFileGenerator {
    private $ConcreteProxyDataGenerator;
    
    public function __construct($ProxyNamespace, $ProxyCachePath) {
        parent::__construct($ProxyNamespace, $ProxyCachePath);
        $this->ConcreteProxyDataGenerator = new ConcreteProxyDataGenerator();
    }
    
    protected function LoadProxyClassFile(\ReflectionClass $EntityReflection, $ProxyClassName, $FullProxyName, $ProxyFileName) {
        $this->GenerateProxyClassFile($ProxyFileName, $ProxyClassName, $EntityReflection);

        require $ProxyFileName;
    }
    
    private function GenerateProxyClassFile($ProxyFileName, $ProxyClassName, \ReflectionClass $EntityReflection) {
        $ProxyClassTemplate = $this->ConcreteProxyDataGenerator->GenerateConcreteProxyData($this->ProxyNamespace, $ProxyClassName, $EntityReflection);
        $this->SaveProxyFile($ProxyFileName, $ProxyClassTemplate);
    }

    private function SaveProxyFile($ProxyFileName, $Template) {
        $DirectoryPath = pathinfo($ProxyFileName, PATHINFO_DIRNAME);
        if (!file_exists($DirectoryPath)) {
            mkdir($DirectoryPath, 0777, true);
        }
        file_put_contents($ProxyFileName, $Template);
    }

}

?>