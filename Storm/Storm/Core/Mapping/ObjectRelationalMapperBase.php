<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object\Expressions\Expression as ObjectExpression;
use Storm\Core\Relational\Expressions\Expression as RelationalExpression;

abstract class ObjectRelationalMapperBase {
    /**
     * @var DomainDatabaseMap 
     */
    protected $DomainDatabaseMap;
    
    public function __construct(DomainDatabaseMap $DomainDatabaseMap) {
        $this->DomainDatabaseMap = $DomainDatabaseMap;
    }
    
    /**
     * @return DomainDatabaseMap
     */
    final public function GetDomainDatabaseMap() {
        return $this->DomainDatabaseMap;
    }
}

?>