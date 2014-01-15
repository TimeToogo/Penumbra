<?php

namespace Storm\Core\Object;

final class DiscardenceData extends EntityData {
    public function __construct(EntityMap $EntityMap, array $EntityData = array()) {
        parent::__construct($EntityMap, $EntityData);
    }
    
    protected function AddProperty(IProperty $Property, $Data) {
        $EntityMap = $this->GetEntityMap();
        $Identifier = $Property->GetIdentifier();
        if(!$EntityMap->HasIdentityProperty($Identifier) &&
                !$EntityMap->HasRelationshipProperty($Identifier)) {
            throw new \InvalidArgumentException('$PropertyName must be a valid Identity or Relationship property of ' . get_class($this->GetEntityMap()));
        }
        
        parent::AddProperty($Property, $Data);
    }
}

?>