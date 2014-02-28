<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Mapping\IEntityRelationalMap;
use \Storm\Core\Mapping\IRelationshipPropertyRelationMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Drivers\Base\Object\MultipleLazyRevivalData;

abstract class RelationshipPropertyRelationMapping extends PropertyMapping implements IRelationshipPropertyRelationMapping {
    private $EntityType;
    /**
     * @var IEntityRelationalMap
     */
    protected $EntityRelationalMap;
    
    /**
     * @var IRelationshipProperty
     */
    private $RelationshipProperty;
    
    /**
     * @var IRelation
     */
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
    
    final public function GetEntityRelationalMap() {
        return $this->EntityRelationalMap;
    }
    
    public function SetEntityRelationalMap(IEntityRelationalMap $EntityRelationalMap) {
        $this->EntityRelationalMap = $EntityRelationalMap;
    }
    
    /**
     * @return Relational\IRelation
     */
    final public function GetRelation() {
        return $this->Relation;
    }
    
    final public function AddToRelationalRequest(Relational\Request $RelationalRequest) {
        $RelationalRequest->AddColumns($this->Relation->GetRelationalParentColumns());
    }
    
    final protected function LoadRelatedRows(Relational\Database $Database, array $ParentRows, Object\RevivalData $AlreadyKnownRevivalData = null) {
        $RelatedRowRequest = $this->Relation->RelationRequest($ParentRows);
        $this->MapEntityToRelationalRequest($RelatedRowRequest, $AlreadyKnownRevivalData);
        return $Database->Load($RelatedRowRequest);
    }
    
    final protected function MapEntityToRelationalRequest(Relational\Request $RelationalRequest, Object\RevivalData $AlreadyKnownRevivalData = null) {
        if($AlreadyKnownRevivalData !== null) {
            $AlreadyKnownPropertyIdentifiers = array_keys($AlreadyKnownRevivalData->GetPropertyData());
            $AlreadyKnownProperties = $AlreadyKnownRevivalData->GetProperties($AlreadyKnownPropertyIdentifiers);
            $this->EntityRelationalMap->MapEntityToRelationalRequest($RelationalRequest, $AlreadyKnownProperties);
        }
        else {
            $this->EntityRelationalMap->MapEntityToRelationalRequest($RelationalRequest);
        }
    }
}

?>