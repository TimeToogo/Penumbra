<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Drivers\Platforms\Base\Queries;

final class IdentifierEscaper extends Queries\IdentifierEscaper {
    public function __construct() {
        parent::__construct('`', '`', '.');
    }
}

?>