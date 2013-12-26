<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Object;
use \Storm\Core\Relational;


final class RevivingContext extends MappingContext {
    public function __construct(DomainDatabaseMap $DomainDatabaseMap) {
        parent::__construct($DomainDatabaseMap);
    }
    
    /**
     * @return Object\RevivalData[]
     */
    public function MapRowsToRevivalData($EntityType, array $RelatedRows) {
        return $this->GetDomainDatabaseMap()->MapRowsToRevivalData($EntityType, $this, $RelatedRows);
    }
    
    public function ReviveEntityInstances(Map $RowInstanceMap) {
        return $this->GetDomainDatabaseMap()->ReviveEntityInstances($this, $RowInstanceMap);
    }
}

?>