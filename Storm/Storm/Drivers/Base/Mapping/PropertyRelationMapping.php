<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Mapping;
use \Storm\Core\Mapping\IPropertyRelationMapping;

abstract class PropertyRelationMapping extends PropertyMapping implements IPropertyRelationMapping {
    private $EntityType;
    private $Relation;
    
    public function __construct( 
            Object\IProperty $Property,
            $EntityType,
            Relational\IRelation $Relation) {
        parent::__construct($Property);
        
        $this->EntityType = $EntityType;
        $this->Relation = $Relation;
    }
    
    public function AddToRelationalRequest(Mapping\DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest) { }
    
    /**
     * @return Relational\IRelation
     */
    final public function GetRelation() {
        return $this->Relation;
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }
}

?>