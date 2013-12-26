<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Relational;

interface IPropertyRelationMapping extends IPropertyMapping {    
    const IPropertyRelationMappingType = __CLASS__;
    
    public function GetEntityType();
    
    /**
     * @return Relational\IRelation
     */
    public function GetRelation();
    
    public function Revive(RevivingContext $Context, Map $ParentRowRevivalDataMap);
    public function AddToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest);
}

?>