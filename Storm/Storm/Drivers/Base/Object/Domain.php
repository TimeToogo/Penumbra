<?php

namespace Storm\Drivers\Base\Object;

use \Storm\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;
use \Storm\Core\Containers\Registrar;

abstract class Domain extends \Storm\Core\Object\Domain {
    public function __construct() {
        parent::__construct();
    }
    
    final protected function RegisterEntityMaps(Registrar $Registrar) {
        $SpecificRegistrar = new Registrar(EntityMap::GetType());
        $this->RegisterAllEntityMaps($Registrar);
        $EntityMaps = $SpecificRegistrar->GetRegistered();
        if($this->HasProxyGenerator()) {
            $this->UpdateProxyGenerator($EntityMaps);
        }
        
        $Registrar->RegisterAll($EntityMaps);
    }
    
    protected abstract function RegisterAllEntityMaps(Registrar $Registrar);
    
    /**
     * @var IProxyGenerator|null
     */
    private $ProxyGenerator = null;
    
    final public function HasProxyGenerator() {
        return $this->ProxyGenerator !== null;
    }
    
    final public function SetProxyGenerator(IProxyGenerator $ProxyGenerator) {
        $this->ProxyGenerator = $ProxyGenerator;
        $this->UpdateProxyGenerator($this->GetEntityMaps());
    }
    
    private function UpdateProxyGenerator(array $EntityMaps) {
        foreach ($EntityMaps as $EntityMap) {
            $EntityMap->SetProxyGenerator($this->ProxyGenerator);
        }
    }
}

?>
