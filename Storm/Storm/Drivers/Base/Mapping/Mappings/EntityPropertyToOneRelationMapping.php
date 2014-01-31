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
    
    final protected function MapParentRowKeysToRelatedRevivalData(DomainDatabaseMap $DomainDatabaseMap, array $ParentRows, array $RelatedRows) {
        $ParentKeyRelatedRowMap = $this->ToOneRelation->MapParentKeysToRelatedRow($ParentRows, $RelatedRows);
        
        $RelatedRevivalDataArray = $DomainDatabaseMap->MapRowsToRevivalData($this->EntityType, $ParentKeyRelatedRowMap);
        
        $MappedRelatedRevivalData = array();
        foreach($ParentRows as $Key => $ParentRow) {            
            $MappedRelatedRevivalData[$Key] = isset($RelatedRevivalDataArray[$Key]) ?
                    $RelatedRevivalDataArray[$Key] : null;
        }
        
        return $MappedRelatedRevivalData;
    }
    
    final protected function MapToParentRowRelatedRevivalDataMap(DomainDatabaseMap $DomainDatabaseMap, Map $ParentRowRevivalDataMap, array $RelatedRows) {
        $ParentRows = $ParentRowRevivalDataMap->GetInstances();
        $ParentRelatedRowMap = $this->ToOneRelation->MapParentKeysToRelatedRow($ParentRows, $RelatedRows);
        
        $RelatedRevivalData = $DomainDatabaseMap->MapRowsToRevivalData($this->GetEntityType(), $RelatedRows);
        $RelatedRowRevivalDataMap = Map::From($RelatedRows, $RelatedRevivalData);

        $ParentRowRelatedRevivalDataMap = new Map();
        foreach($ParentRowRevivalDataMap as $ParentRow) {
            $RelatedRow = $ParentRelatedRowMap[$ParentRow];
            if($RelatedRow !== null) {
                $RelatedRevivalData = $RelatedRowRevivalDataMap[$RelatedRow];

                $ParentRowRelatedRevivalDataMap[$ParentRow] = $RelatedRevivalData;
            }
        }
        
        return $ParentRowRelatedRevivalDataMap;
    }
    
    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, Relational\RelationshipChange $RelationshipChange) {
        if($RelationshipChange->HasDiscardedRelationship() || $RelationshipChange->HasPersistedRelationship()) {
            $this->ToOneRelation->Persist($Transaction, $ParentData, $RelationshipChange);
        }
    }
}

?>