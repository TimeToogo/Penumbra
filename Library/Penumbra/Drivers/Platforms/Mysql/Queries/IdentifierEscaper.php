<?php

namespace Penumbra\Drivers\Platforms\Mysql\Queries;

use \Penumbra\Drivers\Platforms\Base\Queries;

final class IdentifierEscaper extends Queries\IdentifierEscaper {
    public function __construct() {
        parent::__construct('`', '`', '.');
    }
}

?>