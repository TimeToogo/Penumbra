<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Relational;

interface IPropertyRelationMapping extends IPropertyMapping {    
    const IPropertyRelationMappingType = __CLASS__;
    
    public function GetEntityType();
    
    public function AddToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest);
    
    /**
     * @return Relational\IRelation
     */
    public function GetRelation();
}

?>