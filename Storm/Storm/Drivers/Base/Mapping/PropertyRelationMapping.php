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
    
    final protected function LoadRelatedRows(Mapping\RevivingContext $Context, Map $ParentRowRevivalDataMap) {
        $RelationalRequest = new Relational\Request(array(), false);
        $DomainDatabaseMap = $Context->GetDomainDatabaseMap();
        $this->Relation->AddParentPredicateToRequest($RelationalRequest, $ParentRowRevivalDataMap->GetInstances());
        $DomainDatabaseMap->MapEntityToRelationalRequest($this->GetEntityType(), $RelationalRequest);
        
        return $DomainDatabaseMap->GetDatabase()->Load($RelationalRequest);
    }
    public function Revive(Mapping\RevivingContext $Context, Map $ParentRowRevivalDataMap) {
        $RelatedRows = $this->LoadRelatedRows($Context, $ParentRowRevivalDataMap);
        
        $ParentRelatedRowsMap = $this->Relation->MapRelatedRows($ParentRowRevivalDataMap->GetInstances(), $RelatedRows);
        $RevivalData = $Context->MapRowsToRevivalData($this->GetEntityType(), $RelatedRows);
        $RelatedRowRevivalDataMap = Map::From($RelatedRows, $RevivalData);
        
        $this->ReviveProperties($ParentRowRevivalDataMap, $ParentRelatedRowsMap, $RelatedRowRevivalDataMap);
    }
    protected abstract function ReviveProperties(
            Map $ParentRowRevivalDataMap,
            Map $ParentRelatedRowsMap,
            Map $RelatedRowRevivalDataMap);
    
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