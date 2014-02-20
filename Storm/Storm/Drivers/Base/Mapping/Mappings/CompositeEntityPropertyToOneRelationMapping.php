<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Mapping\IEntityPropertyToOneRelationMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Drivers\Base\Object\MultipleLazyRevivalData;

class CompositeEntityPropertyToOneRelationMapping extends CompositeRelationshipPropertyRelationMapping implements IEntityPropertyToOneRelationMapping {
    public function __construct(
            Object\IEntityProperty $EntityProperty, 
            Relational\IToOneRelation $ToOneRelation, 
            $LoadingMode) {
        parent::__construct($EntityProperty, $ToOneRelation, $LoadingMode);
    }
    
    protected function GetConcreteRelationshipMapping($LoadingMode, Object\IRelationshipProperty $RelationshipProperty, Relational\IRelation $Relation) {
        switch ($LoadingMode) {
            case LoadingMode::Eager:
                return new EagerEntityPropertyToOneRelationMapping($RelationshipProperty, $Relation);
            case LoadingMode::SemiLazy:
                return new SemiLazyEntityPropertyToOneRelationMapping($RelationshipProperty, $Relation);
            case LoadingMode::Lazy:
                return new LazyEntityPropertyToOneRelationMapping($RelationshipProperty, $Relation);
            case LoadingMode::ExtraLazy:
                return new ExtraLazyEntityPropertyToOneRelationMapping($RelationshipProperty, $Relation);
        }
    }

    public function AddToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest) {
        return $this->ConcreteRelationshipMapping->AddToRelationalRequest($DomainDatabaseMap, $RelationalRequest);
    }

    public function GetProperty() {
        return $this->ConcreteRelationshipMapping->GetProperty();
    }

    public function GetEntityProperty() {
        return $this->ConcreteRelationshipMapping->GetEntityProperty();
    }

    public function GetToOneRelation() {
        return $this->ConcreteRelationshipMapping->GetToOneRelation();
    }

    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, Relational\RelationshipChange $RelationshipChange) {
        return $this->ConcreteRelationshipMapping->Persist($Transaction, $ParentData, $RelationshipChange);
    }

    public function Revive(DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array $RevivalDataArray) {
        return $this->ConcreteRelationshipMapping->Revive($DomainDatabaseMap, $ResultRowArray, $RevivalDataArray);
    }

}

?>