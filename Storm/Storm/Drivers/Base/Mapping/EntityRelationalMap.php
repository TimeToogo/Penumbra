<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Mapping;

abstract class EntityRelationalMap extends Mapping\EntityRelationalMap {
    private $MappingConfiguration;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * @return IMappingConfiguration
     */
    final protected function GetMappingConfiguration() {
        return $this->MappingConfiguration;
    }
    
    final protected function GetProxyGenerator() {
        return $this->MappingConfiguration->GetProxyGenerator();
    }

    protected function OnInitialize(Mapping\DomainDatabaseMap $DomainDatabaseMap) {
        $this->MappingConfiguration = $DomainDatabaseMap->GetMappingConfiguration();
    }
}

?>