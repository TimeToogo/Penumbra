<?php

namespace Storm\Drivers\Platforms\SQLite;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Core\Relational\Expressions\Expression as CoreExpression;
use \Storm\Drivers\Base\Relational\Expressions as E;
use \Storm\Core\Relational\Expressions as EE;
use \Storm\Drivers\Base\Relational\Expressions\Operators as O;

final class FunctionMapper implements E\IFunctionMapper {
    public function MapFunctionCallExpression($FunctionName, array $ArgumentValueExpression = array()) {
        throw new \Exception();
    }

}

?>