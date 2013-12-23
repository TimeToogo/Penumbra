<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;


final class RevivingContext extends MappingContext {
    public function __construct(DomainDatabaseMap $DomainDatabaseMap) {
        parent::__construct($DomainDatabaseMap);
    }
    
    public function ReviveEntities($EntityType, array $RelatedRows) {
        $Entities = $this->GetDomainDatabaseMap()->ReviveEntities($EntityType, $this, $RelatedRows);
        
        return $Entities;
    }
    
    public function ReviveEntityInstances(Map $RowInstanceMap) {
        return $this->GetDomainDatabaseMap()->ReviveEntityInstances($this, $RowInstanceMap);
    }
}

?>