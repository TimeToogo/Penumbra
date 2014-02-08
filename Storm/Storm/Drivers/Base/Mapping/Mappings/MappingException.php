<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class MappingException extends Mapping\MappingException {
    public static function OptionalEntityInLazyContext(Relational\IToOneRelation $ToOneRelation) {
        throw new self(
                    'Cannot map an optional entity property to relation %s in a lazy context, the relationship must be required',
                    get_class($ToOneRelation));
    }
}

?>