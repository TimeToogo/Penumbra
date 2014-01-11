<?php

namespace Storm\Api\Base;

use \Storm\Core\Mapping\DomainDatabaseMap;

class Storm {
    private $DomainDatabaseMap;
    
    public function __construct(DomainDatabaseMap $DomainDatabaseMap) {
        $this->DomainDatabaseMap = $DomainDatabaseMap;
    }
    
    public function GetRepository($EntityType, $AutoSave = false) {
        if(is_object($EntityType)) {
            $EntityType = get_class($EntityType);
        }
        
        return $this->ConstructRepository($EntityType, $AutoSave);
    }
    /**
     * @return Repository
     */
    protected function ConstructRepository($EntityType, $AutoSave = false) {
        return new Repository($this->DomainDatabaseMap, $EntityType, $AutoSave);
    }
    
    /**
     * @return DomainDatabaseMap
     */
    public function GetDomainDatabaseMap() {
        return $this->DomainDatabaseMap;
    }
}

?>
