<?php

namespace Storm\Core\Object;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class EntityPropertyData extends EntityData {
    
    public function __construct(EntityMap $EntityMap, array $EntityProperties, array $EntityData = array()) {
        parent::__construct($EntityMap, $EntityProperties, $EntityData);
    }
    
    /**
     * @return Identity
     */
    final public function GetIdentity() {
        $EntityMap = $this->GetEntityMap();
        $Identity = $EntityMap->Identity();
        foreach($EntityMap->GetIdentityProperties() as $Property) {
            if(isset($this[$Property])) {
                $Identity[$Property] = $this[$Property];
            }
        }
        
        return $Identity;
    }
}

?>