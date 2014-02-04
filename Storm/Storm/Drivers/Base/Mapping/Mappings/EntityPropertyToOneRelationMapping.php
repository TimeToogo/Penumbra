<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\IEntityPropertyToOneRelationMapping;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Object\LazyRevivalData;

abstract class EntityPropertyToOneRelationMapping extends RelationshipPropertyRelationMapping implements IEntityPropertyToOneRelationMapping {
    private $EntityProperty;
    private $ToOneRelation;
    
    public function __construct(
            Object\IEntityProperty $EntityProperty, 
            Relational\IToOneRelation $ToOneRelation) {
        parent::__construct($EntityProperty, $ToOneRelation);
        
        $this->EntityProperty = $EntityProperty;
        $this->ToOneRelation = $ToOneRelation;
    }

    /**
     * @return Object\IEntityProperty
     */
    final public function GetEntityProperty() {
        return $this->EntityProperty;
    }
    /**
     * @return Relational\IToOneRelation
     */
    final public function GetToOneRelation() {
        return $this->ToOneRelation;
    }    
    
    final protected function MapParentRowKeysToRelatedRevivalData(DomainDatabaseMap $DomainDatabaseMap, array $ParentRows, array $RelatedRows) {
        $ParentKeyRelatedRowMap = $this->ToOneRelation->MapParentKeysToRelatedRow($ParentRows, $RelatedRows);
        
        $RelatedRevivalDataArray = $DomainDatabaseMap->MapRowsToRevivalData($this->GetEntityType(), $ParentKeyRelatedRowMap);
        
        $MappedRelatedRevivalData = array();
        foreach($ParentRows as $Key => $ParentRow) {            
            $MappedRelatedRevivalData[$Key] = isset($RelatedRevivalDataArray[$Key]) ?
                    $RelatedRevivalDataArray[$Key] : null;
        }
        
        return $MappedRelatedRevivalData;
    }
    
    final protected function MakeLazyRevivalData(
            DomainDatabaseMap $DomainDatabaseMap,
            Relational\ResultRow $ParentData,
            callable $RevivalDataLoader) {
        $RelatedData = $DomainDatabaseMap->GetEntityRelationalMap($this->GetEntityType())->ResultRow();
        $this->ToOneRelation->MapRelationalParentDataToRelatedData($ParentData, $RelatedData);
        $AlreadyKnownRelatedRevivalData = 
                $DomainDatabaseMap->MapResultRowDataToRevivalData($this->GetEntityType(), $RelatedData);
        
        return new LazyRevivalData($AlreadyKnownRelatedRevivalData, $RevivalDataLoader);
    }
    
    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, Relational\RelationshipChange $RelationshipChange) {
        if($RelationshipChange->HasDiscardedRelationship() || $RelationshipChange->HasPersistedRelationship()) {
            $this->ToOneRelation->Persist($Transaction, $ParentData, $RelationshipChange);
        }
    }
}

?>