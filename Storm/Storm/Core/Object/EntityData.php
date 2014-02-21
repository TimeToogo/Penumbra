<?php

namespace Storm\Core\Object;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class EntityData extends PropertyData {
    /**
     * @var EntityMap
     */
    protected $EntityMap;
    /**
     * @var string
     */
    protected $EntityType;
    
    public function __construct(EntityMap $EntityMap, array $EntityProperties, array $EntityData = []) {
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
}

?>