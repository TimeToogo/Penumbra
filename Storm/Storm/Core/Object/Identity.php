<?php

namespace Storm\Core\Object;

/**
 * The identity represents the global identity of an entity.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Identity extends EntityData {
    public function __construct(EntityMap $EntityMap, array $IdentityData = []) {
        parent::__construct($EntityMap, $EntityMap->GetIdentityProperties(), $IdentityData);
    }
    
    /**
     * Creates a unique hash value from the identity
     * 
     * @return string The hash of the entity
     */
    final public function Hash() {
        $PropertyData = $this->GetPropertyData();
        ksort($PropertyData);
        return md5(json_encode($PropertyData));
    }
}

?>