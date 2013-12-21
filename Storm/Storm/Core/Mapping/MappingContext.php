<?php

namespace Storm\Core\Mapping;

abstract class MappingContext {
    private $DomainDatabaseMap;
    public function __construct(DomainDatabaseMap $DomainDatabaseMap) {
        $this->DomainDatabaseMap = $DomainDatabaseMap;
    }
    
    /**
     * 
     * @return DomainDatabaseMap
     */
    final public function GetDomainDatabaseMap() {
        return $this->DomainDatabaseMap;
    }
}

?>