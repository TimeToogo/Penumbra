<?php

namespace Storm\Core\Object;

final class RevivalData extends PropertyData {
    public function __construct(EntityMap $EntityMap, array $EntityData = array()) {
        parent::__construct($EntityMap, $EntityData);
    }
}

?>