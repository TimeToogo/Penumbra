<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Mapping\ICollectionPropertyToManyRelationMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

abstract class CollectionPropertyToManyRelationMapping extends PropertyMapping implements ICollectionPropertyToManyRelationMapping {
    private $CollectionProperty;
    private $EntityType;
    private $ToManyRelation;
    
    public function __construct(
            Object\ICollectionProperty $CollectionProperty, 
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($CollectionProperty);
        
        $this->CollectionProperty = $CollectionProperty;
        $this->EntityType = $CollectionProperty->GetEntityType();
        $this->ToManyRelation = $ToManyRelation;
    }

    /**
     * @return Object\ICollectionProperty
     */
    final public function GetCollectionProperty() {
        return $this->CollectionProperty;
    }

    final public function GetEntityType() {
        return $this->EntityType;
    }

    /**
     * @return Relational\IToManyRelation
     */
    final public function GetToManyRelation() {
        return $this->ToManyRelation;
    }
    
    public function AddToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest) {
        
    }
    
    final protected function LoadRelatedRows(DomainDatabaseMap $DomainDatabaseMap, array $ParentRows) {
        $RelatedRowRequest = $this->ToManyRelation->RelationRequest($ParentRows);
        $DomainDatabaseMap->MapEntityToRelationalRequest($this->EntityType, $RelatedRowRequest);
        return $DomainDatabaseMap->GetDatabase()->Load($RelatedRowRequest);
    }
    
    final protected function MapParentRowKeysToRelatedRows(DomainDatabaseMap $DomainDatabaseMap, array $ParentRows, array $RelatedRows) {
        $RelatedRevivalData = $DomainDatabaseMap->MapRowsToRevivalData($this->EntityType, $RelatedRows);

        $PropertyIdentifier = $this->GetProperty()->GetIdentifier();
        $MappedRelatedRows = array();
        foreach($ParentRows as $Key => $ParentRow) {            
            $MappedRelatedRows[$Key] = array_intersect_key($RelatedRevivalData, $RelatedRows);
        }
    }

    public function Persist(Relational\Transaction $Transaction, array &$ParentData, array $RelationshipChanges) {
        if(count($RelationshipChanges) > 0) {
            $this->ToManyRelation->Persist($Transaction, $ParentData, $RelationshipChanges);
        }
    }
}

?>