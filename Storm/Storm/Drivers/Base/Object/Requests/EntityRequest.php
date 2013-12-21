<?php

namespace Storm\Drivers\Base\Object\Requests;

use \Storm\Core\Object;

class EntityRequest extends Request {
    public function __construct(Object\EntityMap $EntityMap, $IsSingleEntity) {
        parent::__construct($EntityMap->GetEntityType(), $EntityMap->GetProperties(), $IsSingleEntity);
    }
}

?>