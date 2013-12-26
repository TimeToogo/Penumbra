<?php

namespace Storm\Core\Object;

final class PersistenceData extends PropertyData {
    public function __construct(EntityMap $EntityMap, array $EntityData = array()) {
        parent::__construct($EntityMap, $EntityData);
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