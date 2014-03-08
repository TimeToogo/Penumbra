<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational;
use \Storm\Core\Relational\Expression as CoreExpression;

/**
 * Well mabe in the future....
 */
interface IResourceMapper {
    /**
     * @return Expression
     */
    public function MapResource($Resource);
}

?>