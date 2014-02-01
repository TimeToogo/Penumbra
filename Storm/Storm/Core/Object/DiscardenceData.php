<?php

namespace Storm\Core\Object;

/**
 * This class represents the data of an entity which is to be discarded.
 * This should contain the entity's identity and its relationship changes.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class DiscardenceData extends EntityPropertyData {
    public function __construct(EntityMap $EntityMap, array $EntityData = array()) {
        parent::__construct(
                $EntityMap, 
                $EntityMap->GetIdentityProperties() + $EntityMap->GetRelationshipProperties(), 
                $EntityData);
    }
}

?>