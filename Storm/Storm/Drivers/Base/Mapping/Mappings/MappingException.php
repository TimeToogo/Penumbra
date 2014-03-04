<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class MappingException extends Mapping\MappingException {
    public static function OptionalEntityInLazyContext(Object\IProperty $Property) {
        return new self(
                    'Cannot map an optional %s entity property to relation in a lazy context, the relationship must be required',
                    $Property->GetIdentifier());
    }
    public static function UnresolvableEntityProperty($EntityType, Object\IProperty $Property) {
        return new MappingException(
                'Unresolvable property for entity %s: %s',
                $EntityType,
                $Property->GetIdentifier());
    }
}

?>