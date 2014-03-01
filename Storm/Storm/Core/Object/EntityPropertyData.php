<?php

namespace Storm\Core\Object;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class EntityPropertyData extends EntityData {
    private $Identity;
    
    public function __construct(EntityMap $EntityMap, array $EntityProperties, array $EntityData = []) {
        parent::__construct($EntityMap, $EntityProperties, $EntityData);
        
        $this->Identity = $EntityMap->Identity();
    }
    
    /**
     * @return Identity
     */
    final public function GetIdentity() {
        return $this->Identity->Another($this->GetPropertyData());
    }
}

?>