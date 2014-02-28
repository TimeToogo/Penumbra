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
    
    final protected function MapParentRowKeysToRelatedRevivalData(Relational\Database $Database, array $ParentRows, array $RelatedRows) {
        $ParentKeyRelatedRowMap = $this->ToOneRelation->MapParentKeysToRelatedRow($ParentRows, $RelatedRows);
        
        $RelatedRevivalDataArray = $this->EntityRelationalMap->MapRowsToRevivalData($Database, $ParentKeyRelatedRowMap);
        
        $MappedRelatedRevivalData = [];
        foreach($ParentRows as $Key => $ParentRow) {            
            $MappedRelatedRevivalData[$Key] = isset($RelatedRevivalDataArray[$Key]) ?
                    $RelatedRevivalDataArray[$Key] : null;
        }
        
        return $MappedRelatedRevivalData;
    }
    
    final protected function MakeLazyRevivalData(
            Relational\Database $Database, 
            Relational\ResultRow $ParentData,
            callable $RevivalDataLoader) {
        $RelatedData = $this->EntityRelationalMap->ResultRow();
        $this->ToOneRelation->MapRelationalParentDataToRelatedData($ParentData, $RelatedData);
        $AlreadyKnownRelatedRevivalData = $this->EntityRelationalMap->MapResultRowsToRevivalData($Database, $RelatedData);
        
        return new LazyRevivalData($AlreadyKnownRelatedRevivalData, $RevivalDataLoader);
    }
    
    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, Relational\RelationshipChange $RelationshipChange) {
        if($RelationshipChange->HasDiscardedRelationship() || $RelationshipChange->HasPersistedRelationship()) {
            $this->ToOneRelation->Persist($Transaction, $ParentData, $RelationshipChange);
        }
    }

    public function MapAssignment(Relational\Criterion $Criterion, Object\Expressions\Expression $AssignmentValueExpression) {
        $this->ToOneRelation->AddRelationToCriterion($Criterion);
        
    }

    public function MapBinary(Relational\Criterion $Criterion, Object\Expressions\Expression $OperandValueExpression) {
        $this->ToOneRelation->AddRelationToCriterion($Criterion);
        
    }

    public function MapObjectOperation(Relational\Criterion $Criterion, Object\Expressions\ObjectOperationExpression $ObjectOperationExpression) {
        $this->ToOneRelation->AddRelationToCriterion($Criterion);
        
    }
}

?>