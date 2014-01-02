<?php

namespace Storm\Core\Object;

final class DiscardenceData extends EntityData {
    public function __construct(EntityMap $EntityMap, array $EntityData = array()) {
        parent::__construct($EntityMap, $EntityData);
    }
    
    protected function AddProperty(IProperty $Property, $Data) {
        if(!$this->GetEntityMap()->HasIdentityProperty($Property->GetIdentifier()))
            throw new \InvalidArgumentException('$PropertyName must be a valid Identity property of ' . get_class($this->GetEntityMap()));
        
        parent::AddProperty($Property, $Data);
    }
}

?>