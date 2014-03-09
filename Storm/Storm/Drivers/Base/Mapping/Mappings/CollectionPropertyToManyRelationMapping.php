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

class CollectionPropertyToManyRelationMapping extends RelationshipPropertyRelationMapping implements ICollectionPropertyToManyRelationMapping {
    private $CollectionProperty;
    private $ToManyRelation;
    private $Loading;
    
    public function __construct(
            Object\ICollectionProperty $CollectionProperty, 
            Relational\IToManyRelation $ToManyRelation,
            Loading\ICollectionLoading $Loading) {
        $Loading->VerifyCompatibility($CollectionProperty);
        
        parent::__construct($CollectionProperty, $ToManyRelation);
        
        $this->CollectionProperty = $CollectionProperty;
        $this->ToManyRelation = $ToManyRelation;
        $this->Loading = $Loading;
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
    
    /**
     * @return Loading\ICollectionLoading
     */
    final public function GetLoading() {
        return $this->Loading;
    }
    
    final public function SetLoading(Loading\ICollectionLoading $Loading) {
        $this->Loading = $Loading;
    }
    
    final public function AddToRelationalSelect(Relational\ResultSetSelect $Select) {
        return $this->Loading->AddToRelationalRequest(
                $this->EntityRelationalMap, 
                $this->ToManyRelation, 
                $Select);
    }
    
    final public function Revive(Relational\Database $Database, array $ResultRowArray, array $RevivalDataArray) {
        $LoadingValues = $this->Loading->Load(
                $this->EntityRelationalMap, 
                $Database, 
                $this->ToManyRelation, 
                $ResultRowArray);
        foreach ($RevivalDataArray as $Key => $RevivalData) {
            $RevivalData[$this->Property] = $LoadingValues[$Key];
        }
    }
    
    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, array $RelationshipChanges) {
        if(count($RelationshipChanges) > 0) {
            $this->ToManyRelation->Persist($Transaction, $ParentData, $RelationshipChanges);
        }
    }

    public function MapFunctionCall(Relational\Criterion $Criterion, Expressions\FunctionCallExpression $FunctionCallExpression) {
        $this->ToManyRelation->AddRelationToCriterion($Criterion);
    }

    public function AddToCriterion(Relational\Criterion $Criterion) {
        
    }

    public function MapPropertyExpression() {
        
    }

}

?>