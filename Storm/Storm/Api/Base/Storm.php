<?php

namespace Storm\Api\Base;

use \Storm\Core\Mapping\DomainDatabaseMap;

/**
 * The Storm class provides an entry point for the api surrounding a DomainDatabaseMap.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Storm {
    /**
     * The supplied DomainDatabaseMap.
     * 
     * @var DomainDatabaseMap
     */
    private $DomainDatabaseMap;
    
    public function __construct(DomainDatabaseMap $DomainDatabaseMap) {
        $this->DomainDatabaseMap = $DomainDatabaseMap;
    }
    
    /**
     * @return DomainDatabaseMap 
     */
    final public function GetDomainDatabaseMap() {
        return $this->DomainDatabaseMap;
    }
    
    /**
     * Builds a new repository instance for an type of entity.
     * 
     * @param string|object $EntityType The entity of which the repository represents
     * @param boolean $AutoSave Whether or not to automatically save every change.
     * @return Repository
     */
    public function GetRepository($EntityType, $AutoSave = false) {
        if(is_object($EntityType)) {
            $EntityType = get_class($EntityType);
        }
        
        return $this->ConstructRepository($EntityType, $AutoSave);
    }
    
    /**
     * Instantiates a new repository.
     * 
     * @return Repository The instantiated repository
     */
    protected function ConstructRepository($EntityType, $AutoSave = false) {
        return new Repository($this->DomainDatabaseMap, $EntityType, $AutoSave);
    }
}

?>
