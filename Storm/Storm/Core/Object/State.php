<?php

namespace Storm\Core\Object;

final class State extends PropertyData {
    public function __construct(EntityMap $EntityMap, array $EntityData = array()) {
        parent::__construct($EntityMap, $EntityData);
    }
    
    /**
     * @return Identity
     */
    final public function GetIdentity() {
        $EntityMap = $this->GetEntityMap();
        $Identity = $EntityMap->Identity();
        foreach($this->GetPropertyData() as $PropertyName => $PropertyData) {
            if($EntityMap->HasIdentityProperty($PropertyName))
                $Identity[$PropertyName] = $PropertyData;
        }
        
        return $Identity;
    }
}

?>