<?php

namespace Storm\Core\Object;

/**
 * This class represents the data of an entity which is to be persisted.
 * This should contain the entity's identity, data values and its relationship changes.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class PersistenceData extends EntityPropertyData {
    public function __construct(EntityMap $EntityMap, array $EntityData = array()) {
        parent::__construct($EntityMap, $EntityMap->GetProperties(), $EntityData);
    }
}

?>