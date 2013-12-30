<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Mapping;

abstract class EntityRelationalMap extends Mapping\EntityRelationalMap {
    private $MappingConfiguration;
    
    /**
     * @return IMappingConfiguration
     */
    final protected function GetMappingConfiguration() {
        return $this->MappingConfiguration;
    }

    protected function OnInitialize(Mapping\DomainDatabaseMap $DomainDatabaseMap) {
        $this->MappingConfiguration = $DomainDatabaseMap->GetMappingConfiguration();
    }
}

?>