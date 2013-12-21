<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;

final class DiscardingContext extends MappingContext {
    private $Identity;
    private $PrimaryKey;
    
    public function __construct(DomainDatabaseMap $DomainDatabaseMap, 
            Object\Identity $Identity, Relational\PrimaryKey $PrimaryKey) {
        parent::__construct($DomainDatabaseMap);
        
        $this->Identity = $Identity;
        $this->PrimaryKey = $PrimaryKey;
    }
    
    /**
     * @return Object\Identity
     */
    public function GetIdentity() {
        return $this->Identity;
    }
    
    /**
     * @return Relational\PrimaryKey
     */
    public function GetPrimaryKey() {
        return $this->PrimaryKey;
    }
}

?>