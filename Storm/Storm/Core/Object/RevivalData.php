<?php

namespace Storm\Core\Object;

/**
 * This class represents the data of an entity which is to be revived.
 * This should contain the entity's identity, data values and relationship revival data.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class RevivalData extends PropertyData {
    public function __construct(EntityMap $EntityMap, array $EntityData = array()) {
        parent::__construct($EntityMap, $EntityData);
    }
}

?>