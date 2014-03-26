<?php

namespace Penumbra\Drivers\Base\Mapping\Mappings;

use \Penumbra\Core\Containers\Map;
use \Penumbra\Core\Mapping\DomainDatabaseMap;
use \Penumbra\Core\Mapping\IEntityRelationalMap;
use \Penumbra\Core\Mapping\IRelationshipPropertyRelationMapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Object\LazyRevivalData;
use \Penumbra\Drivers\Base\Object\MultipleLazyRevivalData;

abstract class RelationshipPropertyRelationMapping extends PropertyMapping implements IRelationshipPropertyRelationMapping {
    private $EntityType;
    /**
     * @var IEntityRelationalMap
     */
    protected $EntityRelationalMap;
    
    /**
     * @var IRelationshipProperty
     */
    private $RelationshipProperty;
    
    /**
     * @var IRelation
     */
    private $Relation;
    
    public function __construct(
            Object\IRelationshipProperty $RelationshipProperty, 
            Relational\IRelation $Relation) {
        parent::__construct($RelationshipProperty);
        
        $this->RelationshipProperty = $RelationshipProperty;
        $this->EntityType = $RelationshipProperty->GetRelatedEntityType();
        $this->Relation = $Relation;
    }

    /**
     * @return Object\ICollectionProperty
     */
    final public function GetRelationshipProperty() {
        return $this->RelationshipProperty;
    }

    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function GetEntityRelationalMap() {
        return $this->EntityRelationalMap;
    }
    
    public function SetEntityRelationalMap(IEntityRelationalMap $EntityRelationalMap) {
        if($EntityRelationalMap->GetEntityType() !== $this->EntityType) {
            throw new MappingException(
                    'Expecting entity relational map for entity %s: %s given',
                    $this->EntityType,
                    $EntityRelationalMap->GetEntityType());
        }
        $this->EntityRelationalMap = $EntityRelationalMap;
    }
    
    /**
     * @return Relational\IRelation
     */
    final public function GetRelation() {
        return $this->Relation;
    }
    
}

?>