<?php

namespace Storm\Core\Object;

/**
 * The identity represents the global identity of an entity.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Identity extends PropertyData {
    public function __construct(EntityMap $EntityMap, array $IdentityData = array()) {
        parent::__construct($EntityMap, $IdentityData);
    }
    protected function AddProperty(IProperty $Property, $Data) {
        if(!$this->EntityMap->HasIdentityProperty($Property->GetIdentifier())) {
            throw new \InvalidArgumentException('$PropertyName must be a valid Identity property of ' . get_class($this->GetEntityMap()));
        }
        
        parent::AddProperty($Property, $Data);
    }
    
    /**
     * Creates a unique hash value from the identity
     * 
     * @return string The hash of the entity
     */
    public function Hash() {
        $PropertyData = $this->GetPropertyData();
        ksort($PropertyData);
        return md5(json_encode($PropertyData));
    }
}

?>