<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Mapping\ICollectionPropertyToManyRelationMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Object\LazyRevivalData;
use \Storm\Drivers\Base\Object\MultipleLazyRevivalData;

abstract class CompositeRelationshipPropertyRelationMapping  {
    protected $LoadingMode;
    
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
    private $ConcreteRelationshipMappings= [];
    
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