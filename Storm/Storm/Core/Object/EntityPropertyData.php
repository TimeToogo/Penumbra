<?php

namespace Storm\Core\Object;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class EntityPropertyData extends PropertyData {
    /**
     * @var IEntityMap
     */
    protected $EntityMap;
    
    /**
     * @var string
     */
    protected $EntityType;
    
    public function __construct(IEntityMap $EntityMap, array $EntityProperties, array $EntityData = []) {
        parent::__construct($EntityProperties, $EntityData);
        
        $this->EntityMap = $EntityMap;
        $this->EntityType = $this->EntityMap->GetEntityType();
    }
    
    /**
     * @return EntityMap
     */
    final public function GetEntityMap() {
        return $this->EntityMap;
    }
    
    /**
     * @return string
     */
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    /**
     * @return string
     */
    final public function Apply($Entity) {
        return $this->EntityMap->Apply($Entity, $this);
    }
}

?>