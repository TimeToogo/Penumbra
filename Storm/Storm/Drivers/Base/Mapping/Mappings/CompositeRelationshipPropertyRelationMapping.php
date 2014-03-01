<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\IEntityRelationalMap;
use \Storm\Core\Mapping\IRelationshipPropertyRelationMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Drivers\Base\Object\MultipleLazyRevivalData;

abstract class CompositeRelationshipPropertyRelationMapping implements IRelationshipPropertyRelationMapping {
    protected $LoadingMode;
    
    /**
     * @var IEntityRelationalMap
     */
    private $EntityRelationalMap;
    
    /**
     * @var IRelationshipProperty
     */
    private $RelationshipProperty;
    
    /**
     * @var IRelation
     */
    private $Relation;
    
    /**
     * @var RelationshipPropertyRelationMapping[] 
     */
    private $ConcreteRelationshipMappings = [];
    
    /**
     * @var RelationshipPropertyRelationMapping
     */
    protected $ConcreteRelationshipMapping;
    
    
    public function __construct(
            Object\IRelationshipProperty $RelationshipProperty, 
            Relational\IRelation $Relation,
            $LoadingMode) {
        $this->RelationshipProperty = $RelationshipProperty;
        $this->Relation = $Relation;
        $this->SetLoadingMode($LoadingMode);
    }
    
    final public function GetEntityRelationalMap() {
        return $this->ConcreteRelationshipMapping->GetEntityRelationalMap();
    }

    final public function GetEntityType() {
        return $this->ConcreteRelationshipMapping->GetEntityType();
    }

    final public function GetProperty() {
        return $this->ConcreteRelationshipMapping->GetRelationshipProperty();
    }

    final public function GetRelation() {
        return $this->ConcreteRelationshipMapping->GetRelation();
    }

    final public function GetRelationshipProperty() {
        return $this->ConcreteRelationshipMapping->GetRelationshipProperty();
    }

    final public function SetEntityRelationalMap(IEntityRelationalMap $EntityRelationalMap) {
        foreach ($this->ConcreteRelationshipMappings as $ConcreteRelationshipMappings) {
            $ConcreteRelationshipMappings->SetEntityRelationalMap($EntityRelationalMap);
        }
    }
    
    public function AddToRelationalRequest(Relational\Request $RelationalRequest) {
        $this->ConcreteRelationshipMapping->AddToRelationalRequest($RelationalRequest);
    }
    
    private function UnsupportedLoadingMode($LoadingMode) {
        return new Mappings\MappingException(
                'The supplied loading mode is unsupported: %s given',
                \Storm\Core\Utilities::GetTypeOrClass($LoadingMode));
    }
    
    final public function SetLoadingMode($LoadingMode) {
        if($LoadingMode === $this->LoadingMode && $this->LoadingMode !== null) {
            return;
        }
        if(!isset($this->ConcreteRelationshipMappings[$LoadingMode])) {
            $ConcreteRelationshipMapping = $this->GetConcreteRelationshipMapping($LoadingMode, $this->RelationshipProperty, $this->Relation);
            if($ConcreteRelationshipMapping === null) {
                $this->UnsupportedLoadingMode($LoadingMode);
            }
            if($this->EntityRelationalMap !== null) {
                $ConcreteRelationshipMapping->SetEntityRelationalMap($this->EntityRelationalMap);
            }
            $this->ConcreteRelationshipMappings[$LoadingMode] = $ConcreteRelationshipMapping;
        }
        
        $this->LoadingMode = $LoadingMode;
        $this->ConcreteRelationshipMapping = $this->ConcreteRelationshipMappings[$LoadingMode];
    }

    /**
     * @return RelationshipPropertyRelationMapping
     */
    protected abstract function GetConcreteRelationshipMapping(
            $LoadingMode, 
            Object\IRelationshipProperty $RelationshipProperty, 
            Relational\IRelation $Relation);
}

?>