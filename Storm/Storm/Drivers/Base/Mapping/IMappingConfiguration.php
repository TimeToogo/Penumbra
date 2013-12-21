<?php

namespace Storm\Drivers\Base\Mapping;

interface IMappingConfiguration {
    public function GetDefaultLoadingMode();
    
    /**
     * @return Proxy\IProxyGenerator
     */
    public function GetProxyGenerator();
}

?>
