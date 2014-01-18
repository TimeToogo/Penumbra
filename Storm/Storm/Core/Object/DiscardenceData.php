<?php

namespace Storm\Core\Object;

/**
 * This class represents the data of an entity which is to be discarded.
 * This should contain the entity's identity and its relationship changes.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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