<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\IEntityPropertyToOneRelationMapping;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;

abstract class EntityPropertyToOneRelationMapping extends PropertyMapping implements IEntityPropertyToOneRelationMapping {
    private $EntityProperty;
    private $EntityType;
    private $ToOneRelation;
    
    public function __construct(
            Object\IEntityProperty $EntityProperty, 
            Relational\IToOneRelation $ToOneRelation) {
        parent::__construct($EntityProperty);
        
        $this->EntityProperty = $EntityProperty;
        $this->EntityType = $EntityProperty->GetEntityType();
        $this->ToOneRelation = $ToOneRelation;
    }

    /**
     * @return Object\IEntityProperty
     */
    final public function GetEntityProperty() {
        return $this->EntityProperty;
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }

    /**
     * @return Relational\IToOneRelation
     */
    final public function GetToOneRelation() {
        return $this->ToOneRelation;
    }
    
    public function AddToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest) {
        
    }
    
    final protected function LoadRelatedRows(DomainDatabaseMap $DomainDatabaseMap, array $ParentRows) {
        $RelatedRowRequest = $this->ToOneRelation->RelationRequest($ParentRows);
        $DomainDatabaseMap->MapEntityToRelationalRequest($this->EntityType, $RelatedRowRequest);
        return $DomainDatabaseMap->GetDatabase()->Load($RelatedRowRequest);
    }
        
    public function Persist(Relational\Transaction $Transaction, array &$ParentData, Relational\RelationshipChange $RelationshipChange) {
        if($RelationshipChange->HasDiscardedRelationship() || $RelationshipChange->HasPersistedRelationship()) {
            $this->ToOneRelation->Persist($Transaction, $ParentData, $RelationshipChange);
        }
    }
}

?>