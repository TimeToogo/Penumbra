<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Mapping\ICollectionPropertyToManyRelationMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Drivers\Base\Object\MultipleLazyRevivalData;

abstract class CollectionPropertyToManyRelationMapping extends RelationshipPropertyRelationMapping implements ICollectionPropertyToManyRelationMapping {
    private $CollectionProperty;
    private $ToManyRelation;
    
    public function __construct(
            Object\ICollectionProperty $CollectionProperty, 
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($CollectionProperty, $ToManyRelation);
        
        $this->CollectionProperty = $CollectionProperty;
        $this->ToManyRelation = $ToManyRelation;
    }

    /**
     * @return Object\ICollectionProperty
     */
    final public function GetCollectionProperty() {
        return $this->CollectionProperty;
    }
    
    /**
     * @return Relational\IToManyRelation
     */
    final public function GetToManyRelation() {
        return $this->ToManyRelation;
    }
    
    final protected function MapParentRowKeysToRelatedRevivalDataArray(DomainDatabaseMap $DomainDatabaseMap, array $ParentRows, array $RelatedRows) {
        $ParentKeyRelatedRowsMap = $this->ToManyRelation->MapParentKeysToRelatedRows($ParentRows, $RelatedRows);
        
        $RelatedRevivalData = $DomainDatabaseMap->MapRowsToRevivalData($this->GetEntityType(), $RelatedRows);
        
        $MappedRelatedRevivalData = array();
        foreach($ParentRows as $Key => $ParentRow) {            
            $MappedRelatedRevivalData[$Key] = array_intersect_key($RelatedRevivalData, $ParentKeyRelatedRowsMap[$Key]);
        }
        
        return $MappedRelatedRevivalData;
    }
    
    final protected function MakeMultipleLazyRevivalData(
            DomainDatabaseMap $DomainDatabaseMap,
            Relational\ResultRow $ParentData,
            callable $RevivalDataLoader) {
        $RelatedData = $DomainDatabaseMap->GetEntityRelationalMap($this->GetEntityType())->ResultRow();
        $this->ToManyRelation->MapRelationalParentDataToRelatedData($ParentData, $RelatedData);
        $AlreadyKnownRelatedRevivalData = 
                $DomainDatabaseMap->MapResultRowDataToRevivalData($this->GetEntityType(), $RelatedData);
        
        return new MultipleLazyRevivalData($AlreadyKnownRelatedRevivalData, $RevivalDataLoader);
    }
    
    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, array $RelationshipChanges) {
        if(count($RelationshipChanges) > 0) {
            $this->ToManyRelation->Persist($Transaction, $ParentData, $RelationshipChanges);
        }
    }
}

?>