<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Mapping\ICollectionPropertyToManyRelationMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Drivers\Base\Object\MultipleLazyRevivalData;

class CompositeCollectionPropertyToManyRelationMapping extends CompositeRelationshipPropertyRelationMapping implements ICollectionPropertyToManyRelationMapping {
    public function __construct(
            Object\ICollectionProperty $CollectionProperty, 
            Relational\IToManyRelation $ToManyRelation, 
            $LoadingMode) {
        parent::__construct($CollectionProperty, $ToManyRelation, $LoadingMode);
    }
    
    protected function GetConcreteRelationshipMapping($LoadingMode, Object\IRelationshipProperty $RelationshipProperty, Relational\IRelation $Relation) {
        switch ($LoadingMode) {
            case LoadingMode::Eager:
                return new EagerCollectionPropertyToManyRelationMapping($RelationshipProperty, $Relation);
            case LoadingMode::SemiLazy:
                return new SemiLazyCollectionPropertyToManyRelationMapping($RelationshipProperty, $Relation);
            case LoadingMode::Lazy:
                return new LazyCollectionPropertyToManyRelationMapping($RelationshipProperty, $Relation);
            case LoadingMode::ExtraLazy:
                return new ExtraLazyCollectionPropertyToManyRelationMapping($RelationshipProperty, $Relation);
        }
    }

    public function AddToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest) {
        return $this->ConcreteRelationshipMapping->AddToRelationalRequest($DomainDatabaseMap, $RelationalRequest);
    }

    public function GetCollectionProperty() {
        return $this->ConcreteRelationshipMapping->GetCollectionProperty();
    }

    public function GetProperty() {
        return $this->ConcreteRelationshipMapping->GetProperty();
    }

    public function GetToManyRelation() {
        return $this->ConcreteRelationshipMapping->GetToManyRelation();
    }

    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, array $RelationshipChanges) {
        return $this->ConcreteRelationshipMapping->Persist($Transaction, $ParentData, $RelationshipChanges);
    }

    public function Revive(DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array $RevivalDataArray) {
        return $this->ConcreteRelationshipMapping->Revive($DomainDatabaseMap, $ResultRowArray, $RevivalDataArray);
    }

}

?>