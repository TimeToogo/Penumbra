<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;

final class PersistingContext extends MappingContext {
    private $State;
    private $ResultRow;
    
    public function __construct(DomainDatabaseMap $DomainDatabaseMap, Object\State $State, Relational\ResultRow $ResultRow) {
        parent::__construct($DomainDatabaseMap);
        
        $this->State = $State;
        $this->ResultRow = $ResultRow;
    }
    
    /**
     * @return Object\State
     */
    public function GetState() {
        return $this->State;
    }
    
    /**
     * @return Relational\ResultRow
     */
    public function GetColumnData() {
        return $this->ResultRow;
    }
}

?>