<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;

final class PersistingContext extends MappingContext {
    private $State;
    private $Row;
    
    public function __construct(DomainDatabaseMap $DomainDatabaseMap, Object\State $State, Relational\Row $Row) {
        parent::__construct($DomainDatabaseMap);
        
        $this->State = $State;
        $this->Row = $Row;
    }
    
    /**
     * @return Object\State
     */
    public function GetState() {
        return $this->State;
    }
    
    /**
     * @return Relational\Row
     */
    public function GetRow() {
        return $this->Row;
    }
}

?>