<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

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
    
    final protected function MapToParentRowRelatedRevivalDataArrayMap(DomainDatabaseMap $DomainDatabaseMap, Map $ParentRowRevivalDataMap, array $RelatedRows) {
        $ParentRows = $ParentRowRevivalDataMap->GetInstances();
        $ParentRelatedRowsMap = $this->ToManyRelation->MapParentToRelatedRows($ParentRows, $RelatedRows);
        
        $RelatedRevivalData = $DomainDatabaseMap->MapRowsToRevivalData($this->GetEntityType(), $RelatedRows);
        $RelatedRowRevivalDataMap = Map::From($RelatedRows, $RelatedRevivalData);

        $ParentRowRelatedRevivalDataArrayMap = new Map();
        foreach($ParentRowRevivalDataMap as $ParentRow) {
            $RelatedRows = $ParentRelatedRowsMap[$ParentRow];
            
            $RelatedRevivalDataArray = new \ArrayObject();
            foreach($RelatedRows as $RelatedRow) {
                $RelatedRevivalDataArray[] = $RelatedRowRevivalDataMap[$RelatedRow];
            }
            
            $ParentRowRelatedRevivalDataArrayMap[$ParentRow] = $RelatedRevivalDataArray;
        }
        
        return $ParentRowRelatedRevivalDataArrayMap;
    }

    
    public function Persist(Relational\Transaction $Transaction, Relational\ColumnData $ParentData, array $RelationshipChanges) {
        if(count($RelationshipChanges) > 0) {
            $this->ToManyRelation->Persist($Transaction, $ParentData, $RelationshipChanges);
        }
    }
}

?>