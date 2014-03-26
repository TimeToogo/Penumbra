<?php

namespace Penumbra\Drivers\Base\Mapping\Mappings;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

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