<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Mapping;
use \Storm\Core\Mapping\IPropertyRelationMapping;

abstract class PropertyRelationMapping extends PropertyMapping implements IPropertyRelationMapping {
    private $EntityType;
    private $Relation;
    
    public function __construct( 
            Object\IProperty $Property,
            $EntityType,
            Relational\IRelation $Relation) {
        parent::__construct($Property);
        
        $this->EntityType = $EntityType;
        $this->Relation = $Relation;
    }
    
    public function AddToRelationalRequest(Mapping\DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest) { }
    
    final protected function LoadRelatedRows(Mapping\RevivingContext $Context, Map $ResultRowStateMap) {
        $RelationalRequest = new Relational\Request(array(), false);
        $DomainDatabaseMap = $Context->GetDomainDatabaseMap();
        $this->Relation->AddParentPredicateToRequest($RelationalRequest, $ResultRowStateMap->GetInstances());
        $DomainDatabaseMap->MapEntityToRelationalRequest($this->GetEntityType(), $RelationalRequest);
        
        return $DomainDatabaseMap->GetDatabase()->Load($RelationalRequest);
    }
    public function Revive(Mapping\RevivingContext $Context, Map $ResultRowStateMap) {
        $RelatedRows = $this->LoadRelatedRows($Context, $ResultRowStateMap);
        
        $ParentRelatedRowsMap = $this->Relation->MapRelatedRows($ResultRowStateMap->GetInstances(), $RelatedRows);
        $RevivedEntities = $Context->ReviveEntities($this->GetEntityType(), $RelatedRows);
        $RelatedRowEntityMap = Map::From($RelatedRows, $RevivedEntities);
        
        $this->ReviveProperties($ResultRowStateMap, $ParentRelatedRowsMap, $RelatedRowEntityMap);
    }
    protected abstract function ReviveProperties(
            Map $ResultRowStateMap,
            Map $ParentRelatedRowsMap,
            Map $RelatedRowEntityMap);
    
    /**
     * @return Relational\IRelation
     */
    final public function GetRelation() {
        return $this->Relation;
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }
}

?>