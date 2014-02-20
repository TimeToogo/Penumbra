<?php

namespace Storm\Drivers\CodeFirst\Object;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Object\Properties\Proxies;

abstract class Domain extends Object\Domain {
    private $ProxyGenerator;
    
    public function __construct() {
        $this->ProxyGenerator = $this->ProxyGenerator();
        parent::__construct();
    }
    
    /**
     * @return Proxies\IProxyGenerator
     */
    protected abstract function ProxyGenerator();
    
    final protected function RegisterEntityMaps(Registrar $Registrar) {
         $SpecificRegistrar = new Registrar(EntityMap::GetType());
         $this->EntityMaps($SpecificRegistrar);
         foreach($SpecificRegistrar->GetRegistered() as $EntityMap) {
             $EntityMap->SetProxyGenerator($this->ProxyGenerator);
         }
         $Registrar->RegisterAll($SpecificRegistrar->GetRegistered());
    }
    protected abstract function EntityMaps(Registrar $Registrar);
}

?>