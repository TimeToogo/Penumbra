<?php

namespace Storm\Core\Object;

final class PersistenceData extends EntityData {
    public function __construct(EntityMap $EntityMap, array $EntityData = array()) {
        parent::__construct($EntityMap, $EntityData);
    }
}

?>