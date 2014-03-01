<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Mapping\ICollectionPropertyToManyRelationMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Drivers\Base\Object\MultipleLazyRevivalData;
use \Storm\Core\Mapping\Expressions;

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
    
    final protected function MapParentRowKeysToRelatedRevivalDataArray(Relational\Database $Database, array $ParentRows, array $RelatedRows) {
        $ParentKeyRelatedRowsMap = $this->ToManyRelation->MapParentKeysToRelatedRows($ParentRows, $RelatedRows);
        
        $RelatedRevivalData = $this->EntityRelationalMap->MapResultRowsToRevivalData($Database, $RelatedRows);
        
        $MappedRelatedRevivalData = [];
        foreach($ParentRows as $Key => $ParentRow) {            
            $MappedRelatedRevivalData[$Key] = array_intersect_key($RelatedRevivalData, $ParentKeyRelatedRowsMap[$Key]);
        }
        
        return $MappedRelatedRevivalData;
    }
    
    final protected function MakeMultipleLazyRevivalData(
            Relational\ResultRow $ParentData,
            callable $RevivalDataLoader) {
        $RelatedData = $this->EntityRelationalMap->ResultRow();
        $this->ToManyRelation->MapRelationalParentDataToRelatedData($ParentData, $RelatedData);
        $AlreadyKnownRelatedRevivalData = 
            $this->EntityRelationalMap->MapResultRowDataToRevivalData([$RelatedData])[0];
        
        return new MultipleLazyRevivalData($AlreadyKnownRelatedRevivalData, $RevivalDataLoader);
    }
    
    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, array $RelationshipChanges) {
        if(count($RelationshipChanges) > 0) {
            $this->ToManyRelation->Persist($Transaction, $ParentData, $RelationshipChanges);
        }
    }

    public function MapFunctionCall(Relational\Criterion $Criterion, Expressions\FunctionCallExpression $FunctionCallExpression) {
        $this->ToManyRelation->AddRelationToCriterion($Criterion);
    }
}

?>