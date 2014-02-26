<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Mapping\ICollectionPropertyToManyRelationMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Drivers\Base\Object\MultipleLazyRevivalData;

abstract class RelationshipPropertyRelationMapping extends PropertyMapping implements IRelationshipPropertyRelationMapping {
    private $EntityType;
    private $RelationshipProperty;
    private $Relation;
    
    public function __construct(
            Object\IRelationshipProperty $RelationshipProperty, 
            Relational\IRelation $Relation) {
        parent::__construct($RelationshipProperty);
        
        $this->RelationshipProperty = $RelationshipProperty;
        $this->EntityType = $RelationshipProperty->GetEntityType();
        $this->Relation = $Relation;
    }

    /**
     * @return Object\ICollectionProperty
     */
    final public function GetRelationshipProperty() {
        return $this->RelationshipProperty;
    }

    final public function GetEntityType() {
        return $this->EntityType;
    }

    /**
     * @return Relational\IRelation
     */
    final public function GetRelation() {
        return $this->Relation;
    }
    
    final public function AddToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest) {
        $RelationalRequest->AddColumns($this->Relation->GetRelationalParentColumns());
    }
    
    final protected function LoadRelatedRows(DomainDatabaseMap $DomainDatabaseMap, array $ParentRows, Object\RevivalData $AlreadyKnownRevivalData = null) {
        $RelatedRowRequest = $this->Relation->RelationRequest($ParentRows);
        $this->MapEntityToRelationalRequest($DomainDatabaseMap, $RelatedRowRequest, $AlreadyKnownRevivalData);
        return $DomainDatabaseMap->GetDatabase()->Load($RelatedRowRequest);
    }
    
    final protected function MapEntityToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest, Object\RevivalData $AlreadyKnownRevivalData = null) {
        if($AlreadyKnownRevivalData !== null) {
            $AlreadyKnownPropertyIdentifiers = array_keys($AlreadyKnownRevivalData->GetPropertyData());
            $AlreadyKnownProperties = $AlreadyKnownRevivalData->GetProperties($AlreadyKnownPropertyIdentifiers);
            $DomainDatabaseMap->MapEntityToRelationalRequest($this->EntityType, $RelationalRequest, $AlreadyKnownProperties);
        }
        else {
            $DomainDatabaseMap->MapEntityToRelationalRequest($this->EntityType, $RelationalRequest);
        }
    }
}

?>